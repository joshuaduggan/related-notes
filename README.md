# Related Notes - Descriptive Links
### github.com/joshuaduggan/related-notes

Easily create and meaningfully relate notes on the web.

**Version 0.2.1** - Branch that switches the categories/notes structure for notes/"descriptive relations".

**Version 0.1.10** - first pre-release version which is developed around a sub-optimal category/note structure with no information about link types.

First truly usable commit of Related Notes that's installable by others. Steps needed to get it up and running are:
- Edit the first defines of public_html/common.php as necessary and create the logins.ini file as described there.
- Import the misc/relatednotesDB.sql into the DB you're using
- Use the sample user of userid:jdoe@mail.com password:secret or create your own using the public_html/dev/makepasswordhash.php and inserting the hash into the DB