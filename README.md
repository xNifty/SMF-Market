SMF-Market
======
Basic market place page hooked into SMF using their SSI.  
Being redone in a new [repository](https://github.com/xNifty/SMFMarket)

About
------ 
Started as a boredom project, continued as a way to practice PHP and learn the better practices for MySQLi.

Requirements
------
1. Simple Machines Forum Software (this makes use of the SMF SSI)
2. MySQL Database
3. The MySQLnd (MySQL Native Driver (for get_result()))

Todo
------ 
1. Finish all class specific TODOs
2. Add banned groups list (same as moderating groups, but as a banned group)
3. Potentially tie in through panel within SMF for changing groups and things far easier
4. Rewrite to use AJAX for live updating without the need to refresh the page (homepage only, not the search page)
5. Add sources list to thank the creators of some images used

Scratched
------
1. ???

Important
------
Effectively abandoned for years, it does work in its current state but could be using bad, out of date practices. Use at your own risk. 
  
General Notes
------
The PM-Subject-URL is an SMF modification to be uploaded through the package manager to allow the PM subjects to be set in the URL (for when a user clicks a name to send a message)

Copyright
------
Released under the MIT License.
