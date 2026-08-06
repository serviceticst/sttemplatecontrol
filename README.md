# Unstick Templates Plugin for GLPI

Starting in GLPI 11, templates for tickets, changes and problems became "sticky".
This means not only are template a way to control the display of the form when creating tickets/changes/problems, but the template used gets saved along with the item so it applies to everyone viewing the item later.
If a self-service user has a template that hides fields, those fields would remain hidden even from technicians.

This plugin reverts the behavior of templates to how they were in GLPI 10 and prior versions by unsetting the template data just before the ticket/change/problem is saved to the database.
GLPI itself will see the template data is missing and will re-calculate it based on the ticket data (entity, cateogry, etc) and the user viewing the form; just as it did before.
