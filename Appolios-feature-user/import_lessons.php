<?php
error_reporting(0);
$con = mysqli_connect("localhost", "root", "", "appolios_db");

// Add all remaining lessons
$lessons = [
    ['Finding Your Idea', '<h2>Business Ideas</h2><p>Identify problems you can solve. Validate with potential customers first.</p>'],
    ['Building a Business Plan', '<h2>Plan Structure</h2><p>Executive summary, market analysis, financials, team, timeline.</p>'],
    ['Funding Your Business', '<h2>Raise Capital</h2><p>Bootstrap first, then consider angel investors or VCs.</p>'],
    ['Marketing Strategy', '<h2>Strategy</h2><p>Define target market, positioning, channels, budget.</p>'],
    ['Scaling Your Business', '<h2>Growth</h2><p>Hire, systematize, delegate.</p>'],
];

$chapterIds = [43, 44, 45, 46, 47];
foreach ($chapterIds as $i => $chapterId) {
    mysqli_query($con, "INSERT INTO lessons (chapter_id, title, lesson_type, content, lesson_order) VALUES ($chapterId, '{$lessons[$i][0]}', 'text', '".mysqli_real_escape_string($con, $lessons[$i][1])."', 1)");
}

echo "Done. Total: " . mysqli_num_rows(mysqli_query($con, "SELECT * FROM lessons")) . "\n";