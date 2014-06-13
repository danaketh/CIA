Continuous Integration Agent
============================

I've tested a lot of CI tools but none of them worked for me. Most of the time they simply lack the necessary
functionality or are pain to use (or don't work at all).

So, after all this I've decided to make my own CI tool. Well, it has the CI in it's name but that's just to hype
your expectations. Truth is, CIA is just a tool to run commands and return their results with some fancy stuff.
Nothing more. All I need is a tool that will run tests, analyze the code and spit out results in a useful way.
Every single CI tool I've tried failed in this (last one failed on broken distro in Docker).

**Current Build Status**

[![Build Status](http://ci.scrion.com/build-status/image/1)](http://ci.scrion.com/build-status/view/1)


1) What do I need?
------------------

- PHP 5.3.3+
- MySQL 5+ or PostgreSQL 9+
- Nginx or Apache web server
- Git
- Composer


2) Why should I switch from my current CI?
------------------------------------------

You should not! If it works for you, there's no reason to switch. If you're looking for something easy to use, with
epic admin and shitload of features, just leave. CIA is not dead simple clickfest app. There's no web interface
except for the test results. I don't need such a thing.

You may give it a try if you're looking for a simple tool that will just build your app and give you the results.
I guess it might be useful in case you're working on some smaller project and want to keep the testing "at home".


3) So why have you released it?
-------------------------------

Because I can. And because there might be people crazy enough to actually use it and maybe even fork it and customize
it in their way.


4) Your code is shit!
---------------------

I know and I don't care. I keep the nice things for proprietary code because that's the one I'm getting paid for.
Grow a pair, fork it and make it better or shut up.


5) Your english sucks!
----------------------

Might be, I'm not a native speaker. Also after 17 years on the internet, I'm using the simple english as I've learned
that lot of it's inhabitants are illiterate imbeciles who can't tell the difference between `your` and `you're` (I can).


6) Your arrogant fuck!
----------------------

I guess you meant **you are**, which is shortened in **you're**. Oh well... But **you're** right. But since you've made
it this far, here, have a cookie.

![Cookie](http://www.john-james-andersen.com/wp-content/uploads/Chocolate-Chip-Cookie.jpg)
