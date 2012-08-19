# A toolbox of functions to help you theme Drupal sites.

Code snippets and documentation for dominating the theme layer and creating efficient, extensible and not least submissive markup.

## Injecting classes
Most of these functions are intended to inject classes for styling into your Drupal site to allow for a more object oriented approach to CSS.

## Expanding template suggestions
Secondly the functions expand on Drupal core's template suggestions and give examples of how to insert custom template suggestions to streamline the number of tpl.php-files in your theme.

## How to use it
 - Copy the functions you need to your template.php
 - Replace any instance of THEMENAME with the system name of the theme.
 - Replace any instance of MENU_NAME or FORMID with the system name of the menu or formID of the form.
 - Adapt, remove or combine conditions as your design dictates.
 - Remove all unneeded code.

## The Render Array And You
Most of these preprocess functions work by manipulating Drupal's render arrays. A reasonable understanding of how they work are really recommended even though just pasting code would probably work 8 times out of 10.

Here is some suggested reading:
- Render Arrays in Drupal 7: http://drupal.org/node/930760
- The Scary Render Array by Erik Stielstra: http://cocoate.com/ddbook/scary-render-array

## Mostly Consistent (and Harmless)
The structure of your render arrays will often vary depending on your base theme and installed modules. Themes like Omega and modules like Context change the structure of the render arrays.

### Inspect render arrays
Remember you can always inspect render arrays.

The best way to do this is to install and enable Devel module (drupal.org/project/devel). This allows you to print out nicely formatted render arrays with either:

- kpr($vars) that prints out the information when the function is run, or
- dsm($vars) that prints out the information in the Drupal messages.

## Contributors
Contributions by:
- Jesper Kristensen: Cableman - https://github.com/cableman
- Marek Sotak: Mareksotak - https://github.com/mareksotak
