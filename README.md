# Comment Access Sync

In Elgg comments and discussion replies have their own access id which controls who can see it.
The access id is initially set to that of the content being commented on, however if the access
of the entity changes, the access of the comments/discussion replies are not updated.

This can lead to confusing situations where some people can see comments that others can't on the same
piece of content.  People replying to comments that other people can't see breaks the flow of
discussion and is generally not an expected behavior.

This plugin does 2 simple tasks.

1. Updates comment/discussion reply access any time an entity is updated
2. Retroactively fixes comment/discussion access on plugin activation and daily cron


## Dependencies

None


## Installation

Unzip to the mod directory of your Elgg installation and activate through the admin plugins page

