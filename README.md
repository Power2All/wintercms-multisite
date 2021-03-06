## Domain-based multisite plugin for WinterCMS

**https://github.com/Power2All/wintercms-multisite**

#### Features:
- Register themes for different domains
- Filter which domains can use backend

#### Warning

This is Keios's plugin, made compatible with WinterCMS. There is also a paid version for OctoberCMS by Voipdeploy, but this one is a free version of it. Possible missing features in the OctoberCMS paid version.

#### How to use:

1. Navigate to Settings -> CMS -> Multisite
2. Enter your desired domain name (Fully Qualified Domain Name required) and select active theme for that domain.
3. Save, that's all, anyone visiting selected domain bound to that WinterCMS instance will see your selected theme.

If you change your themes without changing selected themes in Multisite settings, plugin will automatically route all non-matching requests to theme selected as default in WinterCMS theme selector. That might complicate things a bit, read further.

#### How to work on multiple themes in one WinterCMS?

As theme selector of WinterCMS will be overridden by this plugin, it will have another role - theme selector will now allow you to switch between themes used for edition in CMS module. However, theme selector is used to select default theme and should anything happen to Multisite configuration, plugin will redirect all non-matching requests to that theme. If you change theme to work on it on a site visited by your guests, it would be a smart move to switch back to your desired default theme in theme selector when you end your work, just in case.


#### Caching

Your settings are saved in database and cached using your configured cache service on every configuration save to avoid unneeded database calls.
