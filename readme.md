# study_day_list
## 项目简介

这是一个专为公务员考生设计的学习时间管理系统，帮助考生高效管理每日学习计划。系统提供实时时间显示、任务管理、进度追踪等功能，支持多用户使用，并特别优化了中文UTF-8支持。

## 主要功能

### 📅 多用户支持

- 每个用户有独立的学习记录
- 只需输入用户名即可开始使用
- 随时可以切换不同用户

### ⏰ 实时时间显示

- 顶部动态显示当前日期和时间
- 每秒自动更新确保时间准确
- 底部显示完整系统时间

### ✅ 智能任务管理

- 自动过滤过期任务
- 特殊处理过夜任务（如睡眠任务）
- 当前任务高亮显示并带呼吸动画效果
- 任务完成状态可标记

### 📊 学习进度追踪

- 显示已完成任务数和剩余任务数
- 进度条直观展示完成率
- 完成所有任务后显示祝贺信息

### 📱 响应式设计

- 完美适配手机屏幕
- 任务列表可滚动查看
- 自适应不同屏幕尺寸

## 技术栈

| 技术         | 用途                   |
| :----------- | :--------------------- |
| PHP          | 后端逻辑处理           |
| MySQL        | 数据存储               |
| HTML5        | 页面结构               |
| CSS3         | 界面样式设计           |
| JavaScript   | 实时时间更新和交互功能 |
| Font Awesome | 图标库                 |

## 安装指南

### 环境要求

- PHP 7.0 或更高版本
- MySQL 5.6 或更高版本
- Web服务器 (如Apache或Nginx)

### 安装步骤

1. **克隆仓库**

```
git clone https://github.com/00ye/study_day_list.git
```


2. **创建数据库**

   - 在MySQL中创建一个名为`exam_schedule`的数据库

   - 使用以下SQL设置字符集：
```
CREATE DATABASE exam_schedule 
     CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
```
3. **配置数据库连接**

   - 打开`index.php`文件
   - 修改数据库连接配置：

```
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "exam_schedule";
```
```
   
4. **部署项目**

   - 将项目文件夹放在Web服务器的根目录下（如XAMPP的htdocs目录）

5. **访问系统**

   - 在浏览器中访问：`http://localhost/`

6. **初始化数据库**

   - 首次访问时，系统会自动创建所需的数据表并导入初始任务数据

## 使用说明

1. **设置用户**
   - 在登录页面输入您的用户名（例如"张三"）
   - 点击"开始学习计划"按钮
2. **查看任务**
   - 系统会自动显示当前任务和未来的任务
   - 已过期的任务不会显示在列表中
   - 当前任务会高亮显示并有呼吸动画效果
3. **标记任务完成**
   - 点击任务旁边的复选框标记任务完成
   - 已完成的任务会显示为灰色带删除线
4. **查看统计信息**
   - 顶部统计区域显示：
     - 已完成任务数
     - 剩余任务数
     - 总体完成率
   - 进度条直观展示当日完成率
5. **切换用户**
   - 点击"切换用户"按钮可以返回用户设置页面

## 数据库设计

系统使用三个数据表进行数据管理：

### users表（用户信息）

| 字段名     | 类型            | 描述           |
| :--------- | :-------------- | :------------- |
| id         | INT(6) UNSIGNED | 主键，自增ID   |
| username   | VARCHAR(50)     | 用户名（唯一） |
| created_at | TIMESTAMP       | 创建时间       |

### schedule_tasks表（任务信息）

| 字段名       | 类型            | 描述         |
| :----------- | :-------------- | :----------- |
| id           | INT(6) UNSIGNED | 主键，自增ID |
| start_time   | TIME            | 任务开始时间 |
| end_time     | TIME            | 任务结束时间 |
| task_name    | VARCHAR(100)    | 任务名称     |
| description  | TEXT            | 任务描述     |
| note         | TEXT            | 备注信息     |
| is_overnight | BOOLEAN         | 是否过夜任务 |

### task_completions表（任务完成记录）

| 字段名          | 类型            | 描述         |
| :-------------- | :-------------- | :----------- |
| id              | INT(6) UNSIGNED | 主键，自增ID |
| user_id         | INT(6) UNSIGNED | 关联用户ID   |
| task_id         | INT(6) UNSIGNED | 关联任务ID   |
| completion_date | DATE            | 完成日期     |
| completed_at    | TIMESTAMP       | 完成时间     |

## 贡献指南

欢迎贡献代码！

## 许可证

本项目采用 [MIT 许可证](https://license/)

```
```
MIT License
Copyright (c) [year] [fullname]
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```


## 联系方式

如有任何问题或建议，请联系项目维护者：
📧 邮箱：[admin@000k.de](https://mailto:admin@000k.de/)