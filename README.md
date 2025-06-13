# My PHP & HTML Online Runner
# PHP-HTML-Online-Runner
Copyright © 2025 Hupan0210. All Rights Reserved.

An online tool for running PHP and HTML code.
...
## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

这个项目源于我一个简单的想法：我想创建一个属于自己的、部署在共享主机上、轻量级且功能强大的在线PHP运行环境。

它无需复杂的后台或数据库，却能提供专业、流畅的开发体验。

经过不断的迭代和优化，现在它已经从一个简单的工具，成长为一个功能完善、性能卓越、安全可靠的专业平台。

---

### ✨ **[在线体验地址 (Live Demo)](https://nlbw.pp.ua/)** ✨

---

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![Lighthouse](https://img.shields.io/badge/Lighthouse-90%2B-brightgreen?style=for-the-badge&logo=lighthouse)
![License](https://img.shields.io/badge/License-MIT-blue.svg?style=for-the-badge)

## 🌟 项目核心特性

我为这个工具精心设计并实现了一系列功能，旨在提供极致的用户体验和开发效率。

### 功能与体验
* ✅ **实时运行**：PHP与HTML/CSS/JS混编代码即时运行，在右侧面板实时预览网页效果。
* ✅ **专业编辑器**：集成了功能强大的 **CodeMirror**，支持PHP语法高亮、行号显示、括号匹配等。
* ✅ **代码一键分享**：通过 `lz-string` 压缩代码，生成唯一的URL分享给他人，便于交流和求助。
* ✅ **代码格式化**：集成了 **Prettier** 及其PHP插件，一键美化凌乱的代码。
* ✅ **设备模拟**：内置桌面、平板、手机三种视图模式，并支持横竖屏切换，方便调试响应式布局。
* ✅ **自动保存**：代码会自动保存到浏览器本地存储，关闭页面也不怕丢失。
* ✅ **多语言支持**：支持中英双语，并能自动检测浏览器语言。
* ✅ **文件操作**：支持上传本地代码文件，或将编辑器中的代码下载到本地。

### 安全与稳定
* 🛡️ **安全沙箱**：后端PHP脚本拥有强大的**函数黑名单**，能精准防范文件读写、命令执行等高危操作。
* 🛡️ **资源限制**：为每次代码执行设置了 **15秒超时** 和 **32MB内存** 的上限，并提供友好的错误提示，防止恶意代码拖垮服务器。
* 🛡️ **自动清理**：独立的 `cleanup.php` 脚本，可配合服务器**计划任务 (Cron Job)** 定期清理意外残留的临时文件，保障服务器整洁。
* 🛡️ **安全加固**：配置了 `robots.txt` 阻止爬虫访问敏感文件，`security.txt` 为安全研究人员提供联系渠道，并修复了 `iframe` 沙箱漏洞。

### 性能与SEO
* 🚀 **极致性能**：通过多轮Lighthouse评测和优化，实现了：
    * 浏览器缓存 (`.htaccess`)
    * 异步加载CSS和字体
    * 按需加载JS库（移动端不加载Prettier）
    * 合并CSS请求
    * 最终在**移动端和桌面端Lighthouse性能得分均达到90+**！
* 📈 **全面SEO优化**：
    * 集成了 **Google Analytics** 并实现延迟加载，优化性能。
    * 配置了专业的元数据 (`description`, `keywords`) 和站点地图 (`sitemap.xml`)。
    * 添加了 **JSON-LD结构化数据**，便于搜索引擎理解网站内容。
* ♿ **高可用性**：通过修复颜色对比度和表单标签问题，**无障碍 (Accessibility) 得分达到100分**，对所有用户都非常友好。

## 🛠️ 技术栈

* **前端**: `HTML5`, `CSS3`, `Vanilla JavaScript (ES6+)`
* **后端**: `PHP`
* **核心库**:
    * `CodeMirror`: 提供代码编辑和高亮功能。
    * `Prettier`: 用于代码格式化。
    * `lz-string`: 用于压缩代码以生成分享链接。

## 🚀 如何部署

您也可以轻松地将这个项目部署在您自己的服务器上。

1.  **下载代码**：将本仓库所有文件下载下来。
2.  **上传文件**：将所有文件（`index.html`, `run.php`, `style.css`等）上传到您的网站根目录。服务器需要支持PHP。
3.  **创建`temp`目录**：在网站根目录，手动创建一个名为 `temp` 的文件夹，并确保PHP对其有写入权限（权限通常设为`755`即可）。
4.  **配置计划任务 (可选但推荐)**：
    * 修改 `cleanup.php` 文件中的 `$secretKey` 为您自己的复杂密码。
    * 在您的主机面板中设置一个Cron Job，定期访问 `https://yourdomain.com/cleanup.php?secret=YOUR_SECRET_KEY` 来自动清理临时文件。
5.  **更新个人信息 (可选)**：
    * 修改 `index.html` 中的Google Analytics ID为您自己的。
    * 修改 `security.txt` 中的联系邮箱。

## 📁 文件结构
/
├── index.html         # 主页面，所有前端逻辑和UI
├── run.php            # 后端PHP代码执行与安全处理核心
├── style.css          # 所有CSS样式（已合并CodeMirror样式）
├── cleanup.php        # “清道夫”脚本，用于删除旧的临时文件
├── .htaccess          # Apache服务器配置文件，用于设置浏览器缓存
├── robots.txt         # 告诉搜索引擎爬虫哪些可以访问
├── sitemap.xml        # 网站地图，帮助SEO
├── security.txt       # 安全策略和联系方式
└── temp/              # 存放临时执行文件的目录（需手动创建

## 展望未来 (V2.0)

这个项目已经达到了一个非常稳定和完善的状态。未来，我可能会探索更多有趣的功能，例如：

* [ ] **代码模板库**: 提供常用PHP代码片段，一键插入。
* [ ] **多文件/标签页支持**: 模拟一个更真实的IDE环境。
* [ ] **自定义主题**: 允许用户切换编辑器的主题。
* [ ] **更详细的错误输出**: 将PHP的错误和`var_dump`等输出，在单独的面板中显示。

## ❤️ 致谢

感谢您关注我的这个个人项目。

从一个简单的想法到功能完善的工具，再到极致的性能优化，这段旅程让我学到了很多。

如果您有任何建议或发现了问题，欢迎通过[功能建议]按钮与我联系，或者在GitHub上提出Issue。

---
*该项目在MIT许可下授权。*
