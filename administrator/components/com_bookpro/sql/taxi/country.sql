alter table #__bookpro_country add `lang_code` varchar(2) default '';
update #__bookpro_country set `lang_code` = LOWER(country_code);
update #__bookpro_country set `lang_code` = "en" where `country_code` = "GB" OR `country_code` = "US";

