Ubilling
========

Ubilling is opensource ISP billing system based on stargazer.

Please visit our official resources:

  * [Project homepage](http://ubilling.net.ua)
  * [Documentation](http://wiki.ubilling.net.ua)
  * [Community forums](http://local.com.ua/forum/forum/144-stargazer-ubilling/)

[![Build Status](https://travis-ci.org/nightflyza/Ubilling.svg?branch=master)](https://travis-ci.org/nightflyza/Ubilling)
[![i18n](https://hosted.weblate.org/widgets/ubilling/-/svg-badge.svg)](https://hosted.weblate.org/engage/ubilling/)

About Vulnerabilities, security etc.
========
Special notice, for all **wannabe** pentesters/white hats/black hats/1337 h4x0rs etc, about vulnerabilities that you think you have already discovered:

If you find that some code or commands are executed somewhere - this means that the system is working and nothing else. This is the normal functionality of this project, and it is intended for it.

Please stop being a moron who constantly writes us something like "OMG, we have discovered the fact that users under root rights or administrator rights, or system configuration rights can execute any code under root rights!".

**This system is intended for** the administration of servers, server clusters and equipment in the telecommunications sector. Yes, under the root rights. Yes, under root access to database. This is the direct purpose of this project. This project does not have other purpose. It would be surprising if a system designed to **execute code under root rights** didn't do this when an administrator (user) logged in with the necessary rights for it. In real world, no one has access to this web-interface except system administrators who alredy have root permissions on this server.

Perhaps you are one of those retarded persons who are also surprised by the fact that ssh or telnet executes some commands if you use a username and password for it. Okay then, you can assume that you've found the same serious vulnerability in Ubilling. Your mom is proud of you, but you are still retard.