WordPress Quicklogin
====================

Ermoeglicht den schnellen Wechsel zwischen verschiedenen Benutzern.

NUR ZUR ENTWICKELUJNG GEEIGNET! NICHT IM PRODUKTIVUMFELD EINSETZEN!

Das Plugin erm�glicht es im Login-Screen sich mit nur einem Klick unter verschiedenen Benutzernamen (bzw. Benutzer-Profilen) anzumelden.
Dies ist bei der Entwickelung h�ufig recht n�tzlich um Projekte aus verschiedenen Rollen heraus zu testen.

Das Plugin ist eher rudiment�r ausgelegt und erlaubt lediglich eine Einstellung via Konstante im Code. HTML und Optik muss ggf. per Hand im Code angepasst werden.
Lediglich die Liste der Benutzer kann �ber den Filter 'quicklogin_users_array' beeinflusst werden. So k�nnen in einem Plugin w�hrend der Entwickelungsarbeit verschiedene Szenarien
getestet werden. Der Filter erwartet ein assoziatives Array mit Benutzername -> Passwort.

Changelog
=========

v0.1.0
Erste Version
	
v0.1.1
- Bessere Darstellung durch eigens Stylesheet
- Benutzer-Rollen werden neben den Benutzernamen ausgegeben (optional)