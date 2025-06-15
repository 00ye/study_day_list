<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

// 数据库连接配置
$servername = "localhost";
$username = "todo_list";
$password = "todo_list";
$dbname = "todo_list";

try {
    // 创建PDO数据库连接（使用utf8mb4字符集）
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    // 设置PDO错误模式为异常
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 创建用户表（如果不存在）
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $conn->exec($sql);
    
    // 创建任务表（如果不存在）
    $sql = "CREATE TABLE IF NOT EXISTS schedule_tasks (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        task_name VARCHAR(100) NOT NULL,
        description TEXT,
        note TEXT,
        is_overnight BOOLEAN DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $conn->exec($sql);
    
    // 创建完成记录表（如果不存在）
    $sql = "CREATE TABLE IF NOT EXISTS task_completions (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(6) UNSIGNED NOT NULL,
        task_id INT(6) UNSIGNED NOT NULL,
        completion_date DATE NOT NULL,
        completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (task_id) REFERENCES schedule_tasks(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $conn->exec($sql);
    
    // 检查任务表是否为空
    $sql = "SELECT COUNT(*) as count FROM schedule_tasks";
    $stmt = $conn->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row['count'] == 0) {
        $tasks = [
            ['06:50:00', '07:00:00', '自然醒/缓冲', '设置闹钟，轻微赖床缓冲', '避免惊醒', 0],
            ['07:00:00', '07:30:00', '起床、洗漱、整理', '冷水洗脸提神，整理内务', '快速清醒', 0],
            ['07:30:00', '08:00:00', '早读/晨间记忆', '复习错题、重点笔记、申论金句、时政热点、常识口诀', '黄金记忆时间', 0],
            ['08:00:00', '08:30:00', '早餐时间', '营养均衡，细嚼慢咽', '可听新闻播客或轻音乐', 0],
            ['08:30:00', '10:00:00', '上午学习时段1', '攻坚克难：行测难点(数量、逻辑) / 申论单一题型精练(概括、分析)', '高度专注，远离干扰', 0],
            ['10:00:00', '10:20:00', '上午休息时间', '务必离开书桌！活动、远眺、喝水、拉伸/眼保健操', '避免刷手机！让大脑休息', 0],
            ['10:20:00', '11:50:00', '上午学习时段2', '巩固提升：行测优势模块提速(言语、资料) / 申论题型练习/范文阅读', '保持专注', 0],
            ['11:50:00', '12:00:00', '上午收尾 & 放松', '简单回顾，整理桌面，放松眼睛', '准备午餐', 0],
            ['12:00:00', '13:00:00', '午餐 & 自由时间', '好好吃饭，可看短节目、听音乐、聊天', '避免长剧/游戏', 0],
            ['13:00:00', '13:40:00', '午睡时间', '保证午休，设定闹钟', '20-40分钟最佳，过长易昏沉', 0],
            ['13:40:00', '14:00:00', '午睡起床 & 清醒缓冲', '洗漱、喝水、简单活动', '完全清醒后再学习', 0],
            ['14:00:00', '15:30:00', '下午学习时段1', '专项突破：行测套题限时训练 / 申论大作文(审题、提纲、开头结尾)', '模拟考试压力', 0],
            ['15:30:00', '15:50:00', '下午休息时间', '离开书桌！活动、补充水果/坚果、深呼吸', '避免信息过载', 0],
            ['15:50:00', '17:20:00', '下午学习时段2', '查漏补缺/拓展：错题深度分析 / 时政系统学习 / 申论素材积累 / 常识梳理', '系统整理，弥补短板', 0],
            ['17:20:00', '18:00:00', '运动时间', '≥30分钟！跑步、快走、跳绳、健身操、瑜伽等', '释放压力，激活身体，提升晚间效率', 0],
            ['18:00:00', '19:00:00', '晚餐 & 自由时间', '轻松用餐，交流，处理杂务，可听新闻', '身心放松', 0],
            ['19:00:00', '19:30:00', '放松 & 准备', '散步、洗澡、整理晚间学习资料', '过渡到学习状态', 0],
            ['19:30:00', '21:00:00', '晚间学习时段', '复盘与总结：回顾全天，整理笔记/错题；申论阅读/素材整理；制定明日计划', '避免新套题/过难知识点，以整理归纳为主', 0],
            ['21:00:00', '21:20:00', '晚间休息', '放松，听轻音乐、冥想、聊天', '让大脑放松', 0],
            ['21:20:00', '22:00:00', '自由安排 & 睡前准备', '处理个人事务，准备次日衣物/用品', '避免电子屏幕蓝光 (手机/电脑/电视)', 0],
            ['22:00:00', '22:30:00', '睡前阅读 / 轻松活动', '读纸质书(非学习)、听舒缓播客/白噪音', '让大脑彻底放松', 0],
            ['22:30:00', '06:50:00', '睡觉', '关灯，营造黑暗安静环境', '保证7-7.5小时高质量睡眠', 1]
        ];
        
        foreach ($tasks as $task) {
            $stmt = $conn->prepare("INSERT INTO schedule_tasks (start_time, end_time, task_name, description, note, is_overnight) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute($task);
        }
    }
} catch(PDOException $e) {
    die("数据库错误: " . $e->getMessage());
}

// 获取当前日期和时间
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

// 用户处理
$user_id = null;
$username = null;
$error = null;

// 设置用户处理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    
    if (empty($username)) {
        $error = "请输入用户名";
    } else {
        try {
            // 检查用户是否存在
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // 用户已存在，使用现有用户
                $user_id = $user['id'];
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
            } else {
                // 创建新用户
                //$stmt = $conn->prepare("INSERT INTO users (username) VALUES (?)");
                //$stmt->execute([$username]);
                //$user_id = $conn->lastInsertId();
                //$_SESSION['user_id'] = $user_id;
                //$_SESSION['username'] = $username;
            }
        } catch(PDOException $e) {
            $error = "用户设置失败: " . $e->getMessage();
        }
    }
}

// 登出处理
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// 检查用户是否已设置
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
}

// 处理任务完成状态更新
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id']) && $user_id) {
    $task_id = intval($_POST['task_id']);
    $is_completed = isset($_POST['completed']) ? 1 : 0;
    
    try {
        // 检查任务是否已完成
        $check_sql = "SELECT id FROM task_completions WHERE user_id = ? AND task_id = ? AND completion_date = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->execute([$user_id, $task_id, $current_date]);
        $existing_record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($is_completed) {
            if (!$existing_record) {
                // 添加完成记录
                $insert_sql = "INSERT INTO task_completions (user_id, task_id, completion_date) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->execute([$user_id, $task_id, $current_date]);
            }
        } else {
            // 删除完成记录
            $delete_sql = "DELETE FROM task_completions WHERE user_id = ? AND task_id = ? AND completion_date = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->execute([$user_id, $task_id, $current_date]);
        }
    } catch(PDOException $e) {
        // 处理错误，但不中断页面
        error_log("更新错误: " . $e->getMessage());
    }
}

// 获取今日任务完成情况统计（仅当用户登录时）
$stats = ['total_tasks' => 0, 'completed_tasks' => 0];
$completion_rate = 0;
$tasks = [];
$current_task = null;

if ($user_id) {
    try {
        // 获取所有任务
        $sql = "SELECT t.*, 
                (CASE WHEN c.id IS NOT NULL THEN 1 ELSE 0 END) AS is_completed,
                (CASE WHEN ? BETWEEN t.start_time AND t.end_time THEN 1 
                     WHEN t.is_overnight = 1 AND (t.start_time <= ? OR ? <= t.end_time) THEN 1
                     ELSE 0 END) AS is_current
                FROM schedule_tasks t
                LEFT JOIN task_completions c 
                  ON t.id = c.task_id 
                  AND c.user_id = ? 
                  AND c.completion_date = ?
                ORDER BY t.start_time";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$current_time, $current_time, $current_time, $user_id, $current_date]);
        $all_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 过滤掉已经结束的任务（除了当前任务）
        foreach ($all_tasks as $task) {
            // 如果任务是过夜的，特殊处理
            if ($task['is_overnight']) {
                // 过夜任务：从22:30到第二天06:50
                if ($current_time >= $task['start_time'] || $current_time <= $task['end_time']) {
                    $tasks[] = $task;
                    if ($task['is_current']) {
                        $current_task = $task;
                    }
                }
            } else {
                // 普通任务：只显示当前时间之后开始的任务
                if ($current_time <= $task['end_time']) {
                    $tasks[] = $task;
                    if ($task['is_current']) {
                        $current_task = $task;
                    }
                }
            }
        }
        
        // 获取统计数据
        $stats_sql = "SELECT 
            (SELECT COUNT(*) FROM schedule_tasks) AS total_tasks,
            (SELECT COUNT(*) FROM task_completions 
             WHERE user_id = ? AND completion_date = ?) AS completed_tasks";
        $stmt = $conn->prepare($stats_sql);
        $stmt->execute([$user_id, $current_date]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 计算完成率
        $completion_rate = $stats['total_tasks'] > 0 ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100) : 0;
        
    } catch(PDOException $e) {
        $error = "查询错误: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>公务员考试学习时间表</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', 'Microsoft YaHei', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1e5799, #207cca);
            color: #333;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
        }
        
        h1 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #1e5799;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        h1 i {
            color: #ff9800;
        }
        
        .date-time {
            font-size: 1.1rem;
            color: #1e5799;
            margin-bottom: 15px;
            font-weight: bold;
            background: rgba(30, 87, 153, 0.1);
            padding: 10px;
            border-radius: 8px;
            display: inline-block;
            min-width: 250px;
        }
        
        .user-info {
            background: #e3f2fd;
            padding: 12px 15px;
            border-radius: 10px;
            margin: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info span {
            font-weight: bold;
            color: #1e5799;
            font-size: 1.1rem;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .btn-logout {
            background: #e74c3c;
            color: white;
        }
        
        .btn-logout:hover {
            background: #c0392b;
        }
        
        .user-form {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            padding: 25px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .user-form h2 {
            color: #1e5799;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #444;
            text-align: left;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .btn-submit {
            background: #1e5799;
            color: white;
            width: 100%;
            padding: 14px;
            font-size: 1.1rem;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 10px;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            background: #154273;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
            background: rgba(231, 76, 60, 0.1);
            padding: 10px;
            border-radius: 8px;
        }
        
        .dashboard {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .stats {
            display: flex;
            justify-content: space-around;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            padding: 20px 10px;
            color: white;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .stat-label {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        .progress-container {
            background: #e0e0e0;
            height: 12px;
            border-radius: 6px;
            margin: 15px 10px 5px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(to right, #ff9800, #ff5722);
            border-radius: 6px;
            transition: width 0.5s ease;
        }
        
        .current-task {
            background: #fff8e1;
            padding: 20px;
            margin: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            text-align: center;
            border-left: 5px solid #ff9800;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 152, 0, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(255, 152, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 152, 0, 0); }
        }
        
        .current-task h3 {
            color: #ff9800;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 1.3rem;
        }
        
        .current-task .task {
            font-size: 1.4rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        
        .current-task .time {
            font-size: 1.1rem;
            color: #666;
            margin-top: 8px;
            font-weight: bold;
            background: rgba(255, 152, 0, 0.1);
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
        }
        
        .task-list {
            padding: 0;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .task-item {
            display: flex;
            padding: 18px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s;
        }
        
        .task-item.current {
            background-color: #e3f2fd;
            border-left: 4px solid #1e5799;
        }
        
        .task-time {
            width: 25%;
            font-weight: bold;
            color: #1e5799;
            font-size: 0.95rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .task-content {
            width: 65%;
        }
        
        .task-title {
            font-weight: bold;
            margin-bottom: 6px;
            color: #1e5799;
            font-size: 1.1rem;
        }
        
        .task-desc {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 4px;
            line-height: 1.4;
        }
        
        .task-note {
            font-size: 0.85rem;
            color: #e91e63;
            font-style: italic;
            margin-top: 5px;
        }
        
        .task-status {
            width: 10%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .checkbox-container {
            position: relative;
            width: 28px;
            height: 28px;
        }
        
        .checkbox-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 28px;
            width: 28px;
            background-color: #eee;
            border-radius: 50%;
            transition: all 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .checkbox-container:hover input ~ .checkmark {
            background-color: #ddd;
        }
        
        .checkbox-container input:checked ~ .checkmark {
            background-color: #4CAF50;
        }
        
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }
        
        .checkbox-container input:checked ~ .checkmark:after {
            display: block;
        }
        
        .checkbox-container .checkmark:after {
            left: 10px;
            top: 5px;
            width: 7px;
            height: 14px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }
        
        .completed .task-title,
        .completed .task-desc {
            color: #888;
            text-decoration: line-through;
        }
        
        footer {
            text-align: center;
            padding: 20px 15px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .motivation {
            font-style: italic;
            margin-top: 5px;
            color: #ffeb3b;
        }
        
        .no-tasks {
            text-align: center;
            padding: 30px;
            color: #666;
            font-size: 1.1rem;
        }
        
        .no-tasks i {
            font-size: 3rem;
            color: #1e5799;
            margin-bottom: 15px;
            display: block;
        }
        
        @media (max-width: 480px) {
            .task-item {
                flex-wrap: wrap;
            }
            
            .task-time {
                width: 100%;
                margin-bottom: 8px;
            }
            
            .task-content {
                width: 80%;
            }
            
            .task-status {
                width: 20%;
            }
            
            .stats {
                flex-direction: column;
                gap: 15px;
            }
            
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-graduation-cap"></i> 公务员考试学习时间表</h1>
            
            <div class="date-time" id="current-time">
                <!-- 实时时间将在这里显示 -->
            </div>
            
            <?php if ($user_id): ?>
                <div class="user-info">
                    <div>当前用户: <span><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <a href="?logout" class="btn btn-logout">切换用户</a>
                </div>
            <?php endif; ?>
        </header>
        
        <?php if (!$user_id): ?>
            <!-- 用户设置区域 -->
            <div class="user-form">
                <h2><i class="fas fa-user"></i> 设置学习用户</h2>
                
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-signature"></i> 请输入您的用户名</label>
                        <input type="text" id="username" name="username" placeholder="例如: 张三" required>
                    </div>
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-play"></i> 开始学习计划
                    </button>
                </form>
                
                <div style="margin-top: 20px; color: #666; font-size: 0.95rem;">
                    <p><i class="fas fa-info-circle"></i> 提示: 输入用户名后系统会自动记录您的学习进度</p>
                    <p><i class="fas fa-sync-alt"></i> 系统会自动过滤已过期的任务</p>
                    <p><i class="fas fa-language"></i> 系统已全面支持UTF-8中文显示</p>
                </div>
            </div>
        <?php else: ?>
            <!-- 用户仪表盘 -->
            <div class="dashboard">
                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $stats['completed_tasks']; ?></div>
                        <div class="stat-label">已完成</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo count($tasks); ?></div>
                        <div class="stat-label">剩余任务</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $completion_rate; ?>%</div>
                        <div class="stat-label">完成率</div>
                    </div>
                </div>
                
                <div class="progress-container">
                    <div class="progress-bar" style="width: <?php echo $completion_rate; ?>%"></div>
                </div>
                
                <?php if ($current_task): ?>
                    <div class="current-task">
                        <h3><i class="fas fa-tasks"></i> 当前任务</h3>
                        <div class="task"><?php echo htmlspecialchars($current_task['task_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="time">
                            <?php echo date('H:i', strtotime($current_task['start_time'])); ?> - 
                            <?php echo date('H:i', strtotime($current_task['end_time'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (count($tasks) > 0): ?>
                    <ul class="task-list">
                        <?php foreach ($tasks as $task): 
                            $is_current = $task['is_current'];
                            $is_completed = $task['is_completed'];
                        ?>
                        <li>
                            <form method="post" class="task-item <?php echo $is_current ? 'current' : ''; ?> <?php echo $is_completed ? 'completed' : ''; ?>">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                
                                <div class="task-time">
                                    <?php echo date('H:i', strtotime($task['start_time'])); ?><br>
                                    <?php echo date('H:i', strtotime($task['end_time'])); ?>
                                </div>
                                
                                <div class="task-content">
                                    <div class="task-title"><?php echo htmlspecialchars($task['task_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="task-desc"><?php echo htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php if (!empty($task['note'])): ?>
                                    <div class="task-note"><?php echo htmlspecialchars($task['note'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="task-status">
                                    <label class="checkbox-container">
                                        <input type="checkbox" name="completed" <?php echo $is_completed ? 'checked' : ''; ?> 
                                            onchange="this.form.submit()">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </form>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="no-tasks">
                        <i class="fas fa-check-circle"></i>
                        <h3>今日所有任务已完成！</h3>
                        <p>太棒了！您已经完成了今天的所有学习任务</p>
                        <p>好好休息，明天继续加油！</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <footer>
            <p>系统时间: <span id="system-time"><?php echo date('Y-m-d H:i:s'); ?></span></p>
            <p class="motivation">坚持是成功的关键！祝你备考顺利，一举成"公"</p>
        </footer>
    </div>
    
    <script>
        // 更新时间显示的函数
        function updateTime() {
            const now = new Date();
            
            // 格式化日期和时间
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            // 更新顶部时间显示
            const currentTimeElement = document.getElementById('current-time');
            if (currentTimeElement) {
                currentTimeElement.innerHTML = `
                    <i class="fas fa-calendar-alt"></i> ${year}年${month}月${day}日 
                    <i class="fas fa-clock"></i> ${hours}:${minutes}:${seconds}
                `;
            }
            
            // 更新底部系统时间
            const systemTimeElement = document.getElementById('system-time');
            if (systemTimeElement) {
                systemTimeElement.textContent = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
            }
            
            // 每10分钟自动刷新页面（更新任务列表）
            if (minutes % 10 === 0 && seconds === '00') {
                location.reload();
            }
        }
        
        // 页面加载后立即更新时间
        updateTime();
        
        // 每秒更新一次时间
        setInterval(updateTime, 1000);
    </script>
</body>
</html>