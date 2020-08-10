# Linkage CMS

English | [日本語](./README_jp.md)

![LinkageCMS](http://contentsviewer.work/Master/LinkageCMS/Images/Logo.jpg)

LinkageCMS is a web content management system with the following three features

* Integration with other systems (e.g. Git, FTP, GitHub, GitLab, OneDrive, Google Drive) via the OS standard file system. 
* An editor-independent, lightweight markup language for content description with outline visibility and readability
* Across directories, Content Search and Content management (auto-tagging, auto-categorization, and related-suggestions) based on  topic model.

## Features
### Integration with other systems
The content management of this system is based on the OS standard file system.
Each content is a single text file, and the content hierarchy is represented by a directory.
The meta-information of the content (e.g. tags, cache) is updated on an access, even if the file is changed without going through the system,
It works correctly. 

Based on the OS standard file system, it allows you to integrate with other file system based systems (e.g. Git, FTP, Github, Gitlab, OneDrive, Google Drive) to manage your contents across systems.

![Integration with other systems](http://contentsviewer.work/Master/LinkageCMS/Images/Integration.jpg)

### Editor-independent outline description
The visibility and readability of the outlines are important to improve the readability and the writeability of the text. 
Our system adopts a light-weight markup language, where the indentation represents the hierarchical structure of the text, considering the visibility and readability of the outlines in the plain text.

All standard editors, with or without outlining feature, can be used to write content to increase visibility and readability of outlines.

![Editor-independent outline description](http://contentsviewer.work/Master/LinkageCMS/Images/OutlineEditorFree.jpg)

### Content Management Across Directories
In this system, the content is managed in the directory by the standard OS file system. 
The problem is the searchability of the content and the management of the content by topics.

The problem of searchability of the content is that the content is categorized by the directory, so that those who know the category name can reach the information they want, while those who do not know the category name can not.
Moreover, considering that the content is generated by the combination of multiple topics (topic model), it is not possible to manage the content in a unified way, and the hierarchical relationship of the contents will change depending on which one is the root. 

Therefore, our CMS provides a fuzzy search across all the contents to improve the searchability. 
The topic-based management features include automatic tagging, presentation of contents that are related to a certain topic, and automatic categorization by topic. The CMS is based on the OS standard file system, but manages the contents beyond the directory.

![Content Management Across Directories](http://contentsviewer.work/Master/LinkageCMS/Images/AcrossDirectories.jpg)

## Feature List
* Directory and content file-based management
* Fast response by using cache
* Content viewing and editing
* Content search
* Content management by topic
* Per-user content management and privacy settings
* Easy to read/write writing support format
* Localization support
* Don't use a database (e.g. MySQL)
* Some level of security in environments where SSL (TLS) is not available
* Integration with cloud storage services (e.g. GitHub, GitLab, Google Drive, OneDrive)

## Environment
The supporting environment for this CMS is as follows. It is also available on free rental servers.

* Apache HTTP Server
* PHP 7.0.x or higher

## Use Case
You are expected to manage personal to medium sized content (up to about 1000 content).
It is recommended for the following people

* For personal use.
* Sharing of medium-sized information in circles, labs, projects, etc.

## License
All scripts in this project are licensed under the [BSD 3-Clause License](./LICENSE) except for the following third party libraries

* Client/ace
    * BSD 3-Clause License
    * <https://github.com/ajaxorg/ace>
* Client/ace-diff
    * MIT License
    * <https://github.com/ace-diff/ace-diff>
* Client/syntaxhighlighter
    * MIT License or GNU General Public License (GPL) Version 3
    * <https://github.com/syntaxhighlighter/syntaxhighlighter>

## More info
For more information on this CMS, please visit [LinkageCMS](http://contentsviewer.work/Master/LinkageCMS/LinkageCMS).
