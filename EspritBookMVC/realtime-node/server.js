const express = require('express');
const http = require('http');
const cors = require('cors');
const { Server } = require('socket.io');
const mysql = require('mysql2/promise');

const PORT = process.env.PORT || 3001;
const DB_HOST = process.env.DB_HOST || 'localhost';
const DB_PORT = Number(process.env.DB_PORT || 3306);
const DB_NAME = process.env.DB_NAME || 'appolios_db';
const DB_USER = process.env.DB_USER || 'root';
const DB_PASS = process.env.DB_PASS || '';
const app = express();
app.use(cors({ origin: true, credentials: true }));

app.get('/health', (_req, res) => {
  res.json({ ok: true, service: 'appolios-realtime' });
});

const server = http.createServer(app);
const io = new Server(server, {
  cors: { origin: true, methods: ['GET', 'POST'] }
});

let db;
const roomHistory = new Map();

async function initDb() {
  db = await mysql.createPool({
    host: DB_HOST,
    port: DB_PORT,
    user: DB_USER,
    password: DB_PASS,
    database: DB_NAME,
    connectionLimit: 10
  });

  await db.query(`
    CREATE TABLE IF NOT EXISTS discussion_messages (
      id INT AUTO_INCREMENT PRIMARY KEY,
      discussion_id INT NOT NULL,
      room VARCHAR(120) NOT NULL,
      user_id INT NOT NULL,
      user_name VARCHAR(120) NOT NULL,
      message TEXT NOT NULL,
      message_type VARCHAR(20) NOT NULL DEFAULT 'text',
      file_url VARCHAR(500) NULL,
      file_name VARCHAR(255) NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_discussion_created (discussion_id, created_at),
      INDEX idx_room_created (room, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  `);

  await db.query(`ALTER TABLE discussion_messages ADD COLUMN IF NOT EXISTS message_type VARCHAR(20) NOT NULL DEFAULT 'text'`);
  await db.query(`ALTER TABLE discussion_messages ADD COLUMN IF NOT EXISTS file_url VARCHAR(500) NULL`);
  await db.query(`ALTER TABLE discussion_messages ADD COLUMN IF NOT EXISTS file_name VARCHAR(255) NULL`);
}

function discussionIdFromRoom(room) {
  const m = /^discussion_(\d+)$/.exec(room);
  return m ? Number(m[1]) : 0;
}

async function loadHistory(room) {
  if (!db) return roomHistory.get(room) || [];
  const discussionId = discussionIdFromRoom(room);
  if (!discussionId) return roomHistory.get(room) || [];
  const [rows] = await db.query(
    `SELECT user_id AS userId, user_name AS userName, message, message_type AS messageType, file_url AS fileUrl, file_name AS fileName, UNIX_TIMESTAMP(created_at) * 1000 AS ts
     FROM discussion_messages
     WHERE discussion_id = ?
     ORDER BY created_at ASC
     LIMIT 100`,
    [discussionId]
  );
  return rows || [];
}

async function persistMessage(packet) {
  if (!db) return;
  const discussionId = discussionIdFromRoom(packet.room);
  if (!discussionId) return;
  await db.query(
    `INSERT INTO discussion_messages (discussion_id, room, user_id, user_name, message, message_type, file_url, file_name)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
    [
      discussionId,
      packet.room,
      packet.userId,
      packet.userName,
      packet.message || '',
      packet.messageType || 'text',
      packet.fileUrl || null,
      packet.fileName || null
    ]
  );
}

function pushHistory(room, message) {
  const list = roomHistory.get(room) || [];
  list.push(message);
  if (list.length > 100) list.shift();
  roomHistory.set(room, list);
}

io.on('connection', (socket) => {
  socket.on('join-room', async (payload = {}) => {
    const room = String(payload.room || '').trim();
    if (!room) return;
    socket.join(room);
    let history = [];
    try {
      history = await loadHistory(room);
    } catch (_e) {
      history = roomHistory.get(room) || [];
    }
    socket.emit('room-history', history);
  });

  socket.on('chat-message', async (payload = {}) => {
    const room = String(payload.room || '').trim();
    const userId = Number(payload.userId || 0);
    const userName = String(payload.userName || 'User').slice(0, 80);
    const message = String(payload.message || '').trim().slice(0, 2000);
    const messageType = String(payload.messageType || 'text').trim().toLowerCase();
    const fileUrl = String(payload.fileUrl || '').trim().slice(0, 500);
    const fileName = String(payload.fileName || '').trim().slice(0, 255);
    if (!room) return;
    if (messageType === 'text' && !message) return;
    if (messageType !== 'text' && !fileUrl) return;

    const packet = {
      room,
      userId,
      userName,
      message,
      messageType,
      fileUrl,
      fileName,
      ts: Date.now()
    };
    pushHistory(room, packet);
    try { await persistMessage(packet); } catch (_e) {}
    io.to(room).emit('chat-message', packet);
  });
});

initDb()
  .then(() => {
    server.listen(PORT, () => {
      console.log(`APPOLIOS realtime server running on http://localhost:${PORT}`);
    });
  })
  .catch((err) => {
    console.error('Failed to initialize realtime DB:', err.message);
    server.listen(PORT, () => {
      console.log(`APPOLIOS realtime server running in memory mode on http://localhost:${PORT}`);
    });
  });
