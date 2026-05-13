const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const mysql = require('mysql2/promise');
const cors = require('cors');
require('dotenv').config({ path: require('path').resolve(__dirname, '..', '.env') });

const PORT = Number(process.env.REALTIME_PORT || 3001);

const DB_HOST = process.env.DB_HOST || 'localhost';
const DB_USER = process.env.DB_USER || 'root';
const DB_PASS = process.env.DB_PASS || '';
const DB_NAME = process.env.DB_NAME || 'appolios_db';

const app = express();
app.use(cors({ origin: true, credentials: true }));

const server = http.createServer(app);
const io = new Server(server, {
  cors: {
    origin: true,
    methods: ['GET', 'POST'],
    credentials: true
  }
});

let pool;

async function getPool() {
  if (pool) return pool;
  pool = mysql.createPool({
    host: DB_HOST,
    user: DB_USER,
    password: DB_PASS,
    database: DB_NAME,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
    charset: 'utf8mb4'
  });
  return pool;
}

async function ensureDb() {
  const p = await getPool();
  // Quick connectivity check
  await p.query('SELECT 1');
}

async function loadRoomHistory(room, limit = 80) {
  const p = await getPool();
  const safeLimit = Math.max(1, Math.min(300, Number(limit) || 80));

  const [rows] = await p.query(
    `SELECT user_id, user_name, message, message_type, file_url, file_name, created_at
     FROM discussion_messages
     WHERE room = ?
     ORDER BY created_at ASC
     LIMIT ${safeLimit}`,
    [room]
  );

  return rows.map((r) => ({
    userId: Number(r.user_id || 0),
    userName: String(r.user_name || 'User'),
    message: String(r.message || ''),
    messageType: String(r.message_type || 'text'),
    fileUrl: r.file_url ? String(r.file_url) : '',
    fileName: r.file_name ? String(r.file_name) : '',
    createdAt: r.created_at ? new Date(r.created_at).toISOString() : new Date().toISOString()
  }));
}

async function saveMessage(payload) {
  const p = await getPool();

  const room = String(payload.room || '');
  const discussionId = Number(payload.discussionId || 0);
  const userId = Number(payload.userId || 0);
  const userName = String(payload.userName || 'User');
  const message = payload.message != null ? String(payload.message) : '';
  const messageType = String(payload.messageType || 'text');
  const fileUrl = payload.fileUrl ? String(payload.fileUrl) : null;
  const fileName = payload.fileName ? String(payload.fileName) : null;

  if (!room || !userId) return;

  await p.query(
    `INSERT INTO discussion_messages
      (discussion_id, room, user_id, user_name, message, message_type, file_url, file_name, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())`,
    [discussionId, room, userId, userName, message, messageType, fileUrl, fileName]
  );
}

function inferDiscussionId(room) {
  // room is like: discussion_12
  const m = String(room || '').match(/discussion_(\d+)/);
  return m ? Number(m[1]) : 0;
}

io.on('connection', (socket) => {
  socket.on('join-room', async (data) => {
    try {
      const room = String((data && data.room) || '');
      if (!room) return;

      socket.join(room);

      const history = await loadRoomHistory(room, 120);
      socket.emit('room-history', history);
    } catch (err) {
      // Do not crash the server on DB errors.
      socket.emit('room-history', []);
    }
  });

  socket.on('chat-message', async (data) => {
    try {
      const room = String((data && data.room) || '');
      if (!room) return;

      const payload = {
        room,
        discussionId: inferDiscussionId(room),
        userId: Number((data && data.userId) || 0),
        userName: String((data && data.userName) || 'User'),
        message: String((data && data.message) || ''),
        messageType: String((data && data.messageType) || 'text'),
        fileUrl: data && data.fileUrl ? String(data.fileUrl) : '',
        fileName: data && data.fileName ? String(data.fileName) : ''
      };

      if (!payload.userId) return;

      await saveMessage(payload);

      io.to(room).emit('chat-message', {
        userId: payload.userId,
        userName: payload.userName,
        message: payload.message,
        messageType: payload.messageType,
        fileUrl: payload.fileUrl,
        fileName: payload.fileName,
        createdAt: new Date().toISOString()
      });
    } catch (err) {
      // Ignore errors
    }
  });
});

app.get('/health', async (req, res) => {
  try {
    await ensureDb();
    res.json({ ok: true });
  } catch (e) {
    res.status(500).json({ ok: false, error: String(e && e.message ? e.message : e) });
  }
});

server.listen(PORT, () => {
  console.log(`[realtime] listening on http://127.0.0.1:${PORT}`);
  console.log(`[realtime] db: ${DB_HOST}/${DB_NAME}`);
});
