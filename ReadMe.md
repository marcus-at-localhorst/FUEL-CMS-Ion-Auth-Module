# ION AUTH MODULE FOR FUEL CMS

This is a [FUEL CMS](http://www.getfuelcms.com) authentication module, using [Ion Auth 3](https://github.com/benedmunds/CodeIgniter-Ion-Auth) by [Ben Edmunds](http://benedmunds.com/ion_auth/) to add a frontend authentication for users, seperated from the Fuel CMS authentication system. 

Some inspiration for this module came from here <https://github.com/lckamal/fuelcms-user-module>.


**This module is highly experimental and needs a proper code review from someone who is more familiar with the inner workings of FUEL CMS. I bet the community would greatly appreciate a working auth module.** 

## What you can do

- Admin groups and users in FUEL CMS Backend
- Provide login to frontend users to a portion of website only for them - completely seperated from FUEL CMS users.

Once you activated your module in `$config['modules_allowed']` in fuel/application/config/MY_fuel.php, you can access all Ion Auth [methods](http://benedmunds.com/ion_auth/#login) via FUEL syntax.

```
$this->fuel->iauth->logged_in(), 
$this->fuel->iauth->in_group('admin'),
$this->fuel->iauth->is_admin(), 
$this->fuel->iauth->get_user_id()
```


## Possible Pitfalls
I tried to keep all Ion Auth code intact and change as little as possible (see also info.txt in folders).

If you see a white page in your frontend check the `controllers/Iauth::_render_page()` method. It uses `$this->fuel->pages->render` and expects a **/views/_variables/iauth.php** file with your page variables (Opt-In Controller).

```php
$data = array_merge($this->fuel->pagevars->retrieve('iauth'), $data);

// if something is not working, check this var and all others :)
// $data['layout'] = 'main';

$output = $this->fuel->pages->render($view, $data,[
	'view_module' => IAUTH_FOLDER,
	'language' => detect_lang()
], True);
```

Also, the `iauth_users_model` utilizes the Ion Auth `update()` and `register()` methods in `on_before_save()`, to not reinvent the wheel and be consistent with Ion Auth's inner workings. I just didn't get around dealing with possible errors (see `TODO:` comments in code)

There are two config files. 

1. iauth.php - the FUEL CMS module config file `$this->fuel->iauth->config()`
2. ion_auth.php - the Ion Auth config file `$this->config->item('identity', 'ion_auth')`

I kept them seperate, because the Ion Auth model loads config/ion_auth.php directly. It needs some more debugging, how to include both files properly and avoid any interferences.

## Installation

### Manual
1. Download the zip file from GitHub:
[https://github.com/daylightstudio/FUEL-CMS-Blog-Module](https://github.com/daylightstudio/FUEL-CMS-Blog-Module)

2. Create a "iauth" folder in fuel/modules/ and place the contents of the iauth module folder in there.

3. Import the install.sql and the iauth/install folder into your database

4. Add "iauth" to the `$config['modules_allowed']` in fuel/application/config/MY_fuel.php

## Why Iauth?

I had to call it `iauth` so it doesn't collude with FUEL CMS `auth`. To call it `user` didn't seem right, because it was easily to confuse with FUEL CMS `users`. And I expected side effects from calling it `ion_auth` since I don't see through all the FUEL CMS autoloading stuff. `iauth` was unique in this regard.
