Continuous Integration Agent
============================

I've tested a lot of CI tools but none of them worked for me. Most of the time they simply lack the necessary
functionality or are pain to use (or don't work at all).

So, after all this I've decided to make my own CI tool. Well, it has the CI in it's name but that's just to hype
your expectations. Truth is, CIA is just a tool to run commands and return their results with some fancy stuff.
Nothing more. All I need is a tool that will run tests, analyze the code and spit out results in a useful way.
Every single CI tool I've tried failed in this (last one failed on broken distro in Docker).

CIA is built on [Symfony](http://symfony.com/what-is-symfony) and Doctrine. Yeah, I'm a bit lazy so instead of writing dozens of SQL queries I'm using entities and all that stuff most hipster kids hate (it's fun because writing plain SQL is more mainstream than using ORM).

**Current Build Status**

There's nothing to build yet (not for the public to be precise).


1) What do I need?
------------------

- PHP 5.3.3+ (that's a guess, I'm using 5.5.12 on dev)
- MySQL 5+ or PostgreSQL 9+
- Nginx (please, **DO NOT** use Apache)
- Git (no, I'm not going to support anything else but you're welcome to add the support yourself)
- Composer
- patience (so at least someone around here has it)


2) Why should I switch from my current CI?
------------------------------------------

You should not! If the current one works for you, there's no reason to switch. If you're looking for something that's easy to use, with epic admin and shitload of features, just leave. CIA is not dead simple clickfest app. There's no web interface except for the test results. I don't need such a thing.

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


7) There's no 8
---------------

Believe me, I've made this list...


8) Guess what? I lied!
----------------------

Just a small note for those who can't live without the information about used license. Right now, I'm considering MIT or some other licence that promotes freedom (so GPL is pretty much out of the game). I'm releasing my code to the public because I think they might find it useful or point out mistakes and help me fix them. In return, I don't really care if they make money from it, use it in their proprietary applications or use it while testing the software for that drone which is buzzing above our house. If Bilderberg group wants to use it for taking control over the world, be my guest. Just keep my copyright there so I can brag about it to my friends. 