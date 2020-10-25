<?php

/***************************************************************************
 * lang_faq.php [deutsch]
 * -------------------
 * begin : Sat Dec 16 2000
 * copyright : (C) 2001 The phpBB Group
 * email : support@phpbb.com
 ****************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 ***************************************************************************/
/***************************************************************************
 * deutsch Translation familiar (Du) version by:
 * Joel Ricardo Zick (Rici) webmaster@forcena-inn.de || http://www.sdc-forum.de
 * Modification formal (Sie) version by:
 * Christian Bachmann bachmann@easy-site.ch || http://www.easy-site.ch
 ***************************************************************************/
//
// To add an entry to your FAQ simply add a line to this file in this format:
// $faq[] = array("question", "answer");
// If you want to separate a section enter $faq[] = array("--","Block heading goes here if wanted");
// Links will be created automatically
//
// DO NOT forget the ; at the end of the line.
// Do NOT put double quotes (") in your FAQ entries, if you absolutely must then escape them ie. \"something\"
//
// The FAQ items will appear on the FAQ page in the same order they are listed in this file
//
$faq[] = ['--', 'Registrieren und Einloggen'];
$faq[] = [
    'Warum kann ich mich nicht einloggen?',
    'Haben Sie sich registriert? Sie müssen sich erst registrieren, bevor Sie sich einloggen können. Wurden Sie vom Board gebannt (in dem Fall erhalten Sie eine Nachricht)? Wenn dem so ist, sollten Sie den Webmaster oder den Forumsadministrator kontaktieren, um herauszufinden, warum. Falls Sie registriert und nicht gebannt sind und sich immer noch nicht einloggen können, dann überprüfen Sie Ihren Usernamen und das Passwort. Normalerweise liegt hier der Fehler, falls nicht, kontaktieren Sie den Forumsadministrator, es könnten eine fehlerhafte Forumskonfiguration vorliegen.',
];
$faq[] = [
    'Warum muss ich mich überhaupt registrieren?',
    'Es kann auch sein, dass Sie das gar nicht müssen, das ist die Entscheidung des Administrators. Auf jeden Fall erhalten Sie nach der Registrierung zusätzliche Funktionen, die Gäste nicht haben, z.B. Avatare, Private Nachrichten, Eintritt in Usergruppen, usw. Es dauert nur wenige Augenblicke sich zu registrieren, Sie sollten es also tun.',
];
$faq[] = [
    'Warum logge ich mich automatisch aus?',
    'Sollten Sie die Funktion <i>Automatisch einloggen</i> beim Einloggen aktiviert haben, bleiben Sie nur für eine gewisse Zeit eingeloggt. Dadurch wird der Mißbrauch Ihres Accounts verhindert. Um eingeloggt zu bleiben, wählen Sie die entsprechende Option beim Einloggen. Dies ist nicht empfehlenswert, wenn Sie an einem fremden Rechner sitzen, z.B. in einer Bücherei oder Universität, im Internetcafé usw.',
];
$faq[] = ["Wie kann ich verhindern, dass man Name in der 'Wer ist online?'-Liste auftaucht?", 'In Ihrem Profil finden Sie die Funktion <i>Onlinestatus verbergen</i>, und wenn Sie diese aktivieren, können nur noch Administratoren Sie in der Liste sehen. Sie zählen dann als versteckter User.'];
$faq[] = ['Ich habe mein Passwort verloren!', 'Kein Problem! Sie können Ihr Passwort resetten. Klicken Sie dazu auf der Loginseite auf <u>Ich habe mein Passwort vergessen</u>, folgen Sie den Anweisungen und Sie sollten recht flott wieder eingeloggt sein.'];
$faq[] = [
    'Ich habe mich registriert, kann mich aber nicht einloggen!',
    'Überprüfen Sie erst, ob Sie den richtigen Benutzernamen und/oder Passwort angegeben haben. Falls sie stimmen, gibt es zwei Möglichkeiten, was passiert ist: Wenn der COPPA Bestimmungen aktiviert sind und Sie die Option <u>Ich bin unter 13 Jahre alt</u> beim Registrieren gewählt haben, müssen Sie den erhaltenen Anweisungen folgen. Falls dies nicht der Fall ist, braucht Ihr Account eine Aktivierung. Auf einigen Boards ist es der Fall, dass eine Registrierung immer erst aktiviert werden muss, entweder von Ihnen selbst oder vom Administrator, bevor Sie sich einloggen können. Beim Registrieren wird es Ihnen gesagt, ob eine Aktivierung benötigt wird. Falls Ihnen eine E-Mail zugesandt wurde, folgen Sie den enthaltenen Anweisungen, falls Sie diese E-Mail nicht erhalten haben, vergewissern Sie sich, dass die E-Mail Adresse korrekt war. Ein Grund für den Gebrauch der Accountaktivierungen ist die Vermeidung von Forumsgesindel. Wenn Sie sicher sind, dass die angegebene E-Mail Adresse richtig ist, kontaktieren Sie den Administrator.',
];
$faq[] = [
    'Ich habe mich vor einiger Zeit registriert, kann mich aber nicht mehr einloggen!',
    'Die Gründe dafür sind meistens, dass Sie entweder einen falschen Usernamen oder ein falsches Passwort eingegeben haben (überprüfen Sie die E-Mail, die Sie vom Board geschickt bekommen haben) oder der Administrator hat Ihren Account gelöscht. Falls letzteres der Fall ist, haben Sie vielleicht mit dem Account noch nichts gepostet? Es ist durch aus üblich, dass Foren regelmäßig User löschen, die nichts gepostet haben, um die Größe der Datenbank zu verringern. Versuchen Sie sich erneut zu registrieren und tauchen Sie wieder ein in die Welt der Diskussionen.',
];
$faq[] = ['--', 'Benutzerangaben und Einstellungen'];
$faq[] = [
    'Wie ändere ich meine Einstellungen?',
    'Ihre Einstellungen (sofern Sie registriert sind) werden in der Datenbank gespeichert. Klicken Sie auf den <u>Profil</u>-Link, um sie zu ändern (wird normalerweise am oberen Bildschirmrand angezeigt, hängt aber vom Style ab). Damit können Sie jede Ihrer Einstellungen ändern',
];
$faq[] = [
    'Die Zeiten stimmen nicht!',
    'Die Zeiten stimmen höchstwahrscheinlich schon, vermutlich haben Sie einfach die angezeigte Zeitzone nicht richtig eingestellt. Falls dem so ist, sollten Sie die Einstellungen Ihres Profils überprüfen, um die Zeitzone, die für sich zutreffend ist, zu wählen. Bitte beachten Sie, dass Sie die Zeitzone nur wechseln können, wenn Sie ein registriertes Mitglied sind. Falls Sie also noch nicht registriert sind, wäre das vielleicht ein guter Grund.',
];
$faq[] = [
    'Ich habe die Zeitzone gewechselt und die Zeit ist immer noch falsch!',
    'Wenn Sie sicher sind, die richtige Zeitzone gewählt zu haben und die Zeiten immer noch nicht stimmen, kann es daran liegen, dass das System auf Sommerzeit steht. Das Board ist nicht dazu erschaffen worden, zwischen Winter- und Sommerzeit zu wechseln, weswegen es im Sommer zu einer Stunde Differenz zwischen Ihrer gewählten und der Boardzeit kommen kann.',
];
$faq[] = [
    'Meine Sprache ist nicht verfügbar!',
    'Die wahrscheinlichsten Gründe sind, dass der Administrator die Sprache nicht installiert hat oder das Board wurde noch nicht in Ihre Sprache übersetzt. Versuchen Sie, den Board-Administrator davon zu überzeugen, Ihr Sprachfile zu installieren oder, falls es nicht existiert, können Sie auch gerne selber eine Übersetzung schreiben. Weitere Informationen erhalten Sie auf der phpBB Group Website (Der Link ist am Ende jeder Seite)',
];
$faq[] = [
    'Wie kann ich ein Bild unter meinem Benutzernamen anzeigen?',
    'Es könenn sich zwei Bilder unter dem Benutzernamen befinden. Das erste gehört zu Ihrem Rang, z.B. Punkte oder Sterne, die anzeigen, wie viele Beiträge Sie geschrieben haben oder welchen Status Sie im Forum haben. Darunter befindet sich meist ein größeres Bild, Avatar genannt. Dies ist normalerweise ein Einzelstück und an den Benutzer gebunden. Es liegt am Administrator, ob er Avatare erlaubt und ob die Benutzer wählen dürfen, wie sie ihren Avatar zugänglich machen. Wenn Sie keine Avatare benutzen können, ist das eine Entscheidung des Administrators. Sie sollten ihn nach dem Grund fragen (Er wird bestimmt einen guten haben).',
];
$faq[] = [
    'Wie kann ich meinen Rang ändern?',
    'Normalerweise können Sie nicht direkt den Wortlaut des Ranges ändern (Ränge erscheinen unter Ihrem Benutzernamen in Themen und in Ihrem Profil, abhängig davon, welchen Style Sie benutzen). Die meisten Boards benutzen Ränge, um anzuzeigen, wie viele Beiträge geschrieben wurden und bestimmte Benutzer, z.B. Moderatoren oder Administratoren, könnten einen speziellen Rang haben. Bitte belästigen Sie das Forum nicht mit unnötigen Beiträgen, nur um Ihren Rang zu erhöhen, sonst werden Sie auf einen Moderator oder Administrator treffen, der Ihren Rang einfach wieder senkt.',
];
$faq[] = [
    'Wenn ich auf den E-Mail Link eines Benutzers klicke, werde ich dazu aufgefordert, mich einzuloggen!',
    'Nur registrierte Benutzer können über das Forum E-Mails verschicken (falls der Administrator diese Funktion zulässt). Damit sollen obszöne Mails von unbekannten Benutzern unterbunden werden.',
];
$faq[] = ['--', 'Beiträge schreiben'];
$faq[] = [
    'Wie schreibe ich ein Thema in ein Forum?',
    'Ganz einfach, klicken Sie auf den entsprechenden Button auf der Forums- oder Beitragsseite. Es kann sein, dass Sie sich erst registrieren müssen, bevor Sie eine Nachricht schreiben können - Ihre verfügbaren Aktionen werden am Ende der Seite aufgelistet (die <i>Sie können neue Themen erstellen, Sie können an Umfragen teilnehmen, usw.<i>-Liste)',
];
$faq[] = [
    'Wie editiere oder lösche ich einen Beitrag?',
    'Sofern Sie nicht der Boardadministrator oder der Forumsmoderator sind, können Sie nur Ihre eigenen Beiträge löschen oder editieren. Sie können einen Beitrag editieren (eventuell nur für eine gewisse Zeit) indem Sie auf den <i>Editieren</i>-Button des jeweiligen Beitrages klicken. Sollte jemand bereits auf den Beitrag geantwortet haben, werden Sie einen kleinen Text unterhalb des Beitrags lesen können, der anzeigt, wie oft der Text bearbeitet wurde. Er wird nur erscheinen, wenn jemand geantwortet hat, ferner wird er nicht erscheinen, falls ein Moderator oder Administrator den Beitrag editiert hat (Sie sollten eine Nachricht hinterlassen, warum sie den Beitrag editierten). Beachte, dass normale Benutzer keine Beiträge löschen können, wenn schon jemand auf sie geantwortet hat.',
];
$faq[] = [
    'Wie kann ich eine Signatur anhängen?',
    'Um eine Signatur an einen Beitrag anzuhängen, müssen Sie erst eine im Profil erstellen. Wenn Sie eine erstellt haben, aktiviere die <i>Signatur anhängen</i>-Funktion während der Beitragserstellung. Sie können auch eine Standardsignatur an alle Beiträge anhängen, indem Sie im Profil die entsprechende Option anwählen (Sie können das Anfügen einer Signatur immer noch verhindern, indem Sie die Signaturoption beim Beitragssschreiben abschalten)',
];
$faq[] = [
    'Wie erstelle ich eine Umfrage?',
    'Eine Umfrage zu erstellen ist recht einfach: Wenn Sie ein neues Thema erstellen, (oder den ersten Beitrag eines Themas editieren, sofern Sie die Erlaubnis haben) sollten Sie die <i>Umfrage hinzufügen</i>-Option unterhalb der Textbox sehen (falls Sie sie nicht sehen können, haben Sie möglicherweise nicht die erforderlichen Rechte). Sie sollten einen Titel für Ihre Umfrage angeben und mindestens eine Antwortmöglichkeit (um eine Möglichkeit anzugeben, klicken Sie auf den <i>Möglichkeit hinzufügen</i> Knopf. Sie können auch ein Zeitlimit für die Umfrage setzen, 0 ist ein unendlich lang andauernde Umfrage. Es gibt automatische Grenze bei der Anzahl an Antwortoptionen, diese setzt der Administrator fest',
];
$faq[] = [
    'Wie editiere oder lösche ich eine Umfrage?',
    'Genau wie mit den Beiträgen, können Umfrage nur vom Verfasser editiert oder gelöscht werden, oder vom Forumsmoderator oder Administrator. Um eine Umfrage zu editieren, editieren Sie den ersten Beitrag im Thema (die Umfrage ist immer damit verbunden). Wenn noch niemand bei der Umfrage mitgestimmt hat, können User die Umfrage editieren oder löschen, falls jedoch schon jemand mitgestimmt hat, können nur Moderatoren oder Administratoren sie löschen oder editieren. Damit soll verhindert werden, dass Personen ihre Umfragen beeinflussen, indem sie Optionen verändern',
];
$faq[] = [
    'Warum kann ich ein Forum nicht betreten?',
    'Manche Foren können nur von bestimmten Benutzern oder Gruppen betreten werden. Um dort hineinzugelangen, Beiträge zu lesen oder zu schreiben usw. könnten Sie eine spezielle Erlaubnis brauchen. Nur der Forumsmoderator und der Boardadministrator können Ihnen die Zugangsrechte dafür geben, Sie sollten sie um Zugang bitten, sofern dies gerechtfertigt ist.',
];
$faq[] = [
    'Warum kann ich bei Abstimmungen nicht mitmachen?',
    'Nur registrierte Benutzer könen an Umfragen teilnehmen. Dadurch wird eine Beeinflussung des Ergebnisses verhindert. Falls Sie sich registriert haben und immer noch nicht mitstimmen können, haben Sie vermutlich nicht die erforderlichen Rechte dazu.',
];
$faq[] = ['--', 'Was man in und mit Beiträgen tun kann'];
$faq[] = [
    'Was ist BBCode?',
    'BBCode ist eine spezielle Abart von HTML. Ob Sie BBCode benutzen können, wird vom Administrator vorgegeben. Sie können es auch in einzelnen Beiträgen deaktivieren. BBCode selber ist HTML sehr ähnlich, die Tags sind von den Klammern [ und ] umschlossen und dies bietet Ihnen große Kontrolle darüber, was und wie etwas angezeigt wird. Für weitere Informationen über den BBCode sollten Sie die Anleitung anschauen, die Sie von der Beiträge Schreiben-Seite aus erreichen können..',
];
$faq[] = [
    'Darf ich HTML benutzen?',
    'Das hängt davon ab, ob es Ihnen vom Administrator erlaubt wurde. Falls Sie es nicht dürfen, werden Sie nachher nur ein Klammer-Wirrwarr wieder finden. Dies ist eine <i>Sicherung</i>, um Leute davon abzuhalten, das Forum mit unnötigen Tags zu überschwemmen, die das Layout zerstören oder andere Störungen hervorrufen könnten. Falls HTML aktiviert wurde, können Sie es immer noch manuell für jeden Beitrag deaktivieren, indem Sie beim Schreiben die entsprechende Option aktivieren.',
];
$faq[] = [
    'Was sind Smilies?',
    'Smilies sind kleine Bilder, die benutzt werden können, um Gefühle auszudrücken. Es werden nur kurze Codes benötigt, z.B. zeigt :) Freude und :( Traurigkeit an. Die komplette Liste der Smilies kann auf der Beitrag Schreiben-Seite gesehen werden. Übertreiben Sie es nicht mit Smilies, es kann schnell passieren, dass ein Beitrag dadurch völlig unübersichtlich wird. Ein Moderator könnte sich entschließen, den Beitrag zu bearbeiten oder sogar komplett zu löschen.',
];
$faq[] = [
    'Darf ich Bilder einfügen?',
    'Bilder können in der Tat im Beitrag angezeigt werden. Auf jeden Fall gibt es noch keine Möglichkeit, Bilder direkt aufs Board hochzuladen. Deshalb müssen Sie zu einem bestehehden Bild verlinken, welches sich auf einem für die Öffentlichkeit zugänglichen Server befindet. z.B. http://www.meineseite.de/bescheuertesbild.gif. Sie können weder zu Bildern linken, die sich auf Ihrer Festplatte befinden (außer es handelt sich um einen öffentlich-verfügbaren Server) noch zu Bildern, die einen Zugang brauchen, um sie anzuzeigen (z.B. E-Mail-Konten, Passwort-geschützte Seiten usw). Um das Bild anzuzeigen, benutzen Sie entweder den BB-Code [img] oder nutzt HMTL (sofern erlaubt).',
];
$faq[] = [
    'Was sind Ankündigungen?',
    'Ankündigungen beinhalten meistens wichtige Informationen und Sie sollten sie so früh wie möglich lesen. Ankündigungen erscheinen immer am Anfang des jeweiligen Forums. Ob Sie eine Ankündigung machen können oder nicht hängt davon ab, was für Befugnisse dazu erstellt wurden. Diese legt der Board Administrator fest.',
];
$faq[] = [
    'Was sind Wichtige Themen?',
    'Wichtige Themen erscheinen unterhalb der Ankündigungen in der Forumsansicht. Sie enthalten auch meistens wichtige Informationen, die Sie gelesen haben sollten. Genau wie mit den Ankündigungen, entscheidet auch bei den Wichtigen Themen der Administrator, wer sie posten darf und wer nicht.',
];
$faq[] = [
    'Was sind geschlossene Themen?',
    'Themen werden entweder vom Forumsmoderator oder dem Board Administrator geschlossen. Man kann auf geschlossene Beiträge nicht antworten und falls eine Umfrage angefügt wurde, wird diese damit auch beendet. Es gibt verschiedene Gründe, warum ein Thema geschlossen wird.',
];
$faq[] = ['--', 'Benutzerebenen und Gruppen'];
$faq[] = [
    'Was sind Administratoren?',
    'Administratoren haben die höchste Kontrollebene im gesamten Forum. Sie haben die Macht, jede Forumsaktion zu unterbinden und Aktionen durchzuführen, wie die Vergabe von Befugnissen, das Bannen von Benutzern, Benutzergruppen erstellen, Moderatoren ernennen usw. Sie haben außerdem die vollen Moderatorenrechte in jedem Forum.',
];
$faq[] = [
    'Was sind Moderatoren?',
    'Moderatoren sind Personen (oder Gruppen) die auf das tägliche Geschehen in dem jeweiligen Forum achten. Sie haben die Möglichkeit, Beiträge zu editieren und zu löschen, Themen zu schließen, öffnen, verschieben oder löschen. Moderatoren haben die Aufgabe, die Leute davon abzuhalten, unpassende Themen in einen Beitrag zu schreiben, oder sonstigen Blödsinn in die Welt zu setzen.',
];
$faq[] = [
    'Was sind Benutzergruppen?',
    'In Benutzergruppen werden einige Benutzer vom Administrator zusammengefasst. Jeder Benutzer kann zu mehreren Gruppen gehören, und jeder Gruppe können spezielle Zugangsrechte erteilt werden. So ist es für den Administrator einfacher, mehrere Benutzer zu Moderatoren eines bestimmten Forums zu erklären, ihnen Rechte für ein privates Forum zu geben und so weiter.',
];
$faq[] = [
    'Wie kann ich einer Benutzergruppe beitreten?',
    'Um einer Benutzergruppe beizutreten, klicken Sie auf den Benutzergruppe-Link im Menü, Sie erhalten dann einen Überblick über alle Benutzergruppen. Nicht alle Gruppen haben <i>offenen Zugang</i>, manche sind geschlossen und andere könnten versteckt sein. Falls die Gruppe Mitglieder zu lässt, können Sie um Einlass in die Gruppe bitten, indem Sie auf den Beitreten-Button klicken. Der Gruppenmoderator muss noch seine Zustimmung geben, eventuell gibt es Rückfragen, warum Sie der Gruppe beitreten möchten. Bitte nerven Sie die Gruppenmoderatoren nicht, nur weil sie Sie nicht in die Gruppe aufnehmen wollen, sie werden ihre Gründe haben.',
];
$faq[] = [
    'Wie werde ich ein Gruppenmoderator?',
    'Benutzergruppen werden vom Board Administrator erstellt, er bestimmt ebenfalls den Moderator. Falls Sie daran interessiert sind, eine Benutzergruppe zu erstellen, sollten Sie zuerst den Administrator kontaktieren, zum Beispiel mit einer Privaten Nachricht.',
];
$faq[] = ['--', 'Private Nachrichten'];
$faq[] = [
    'Ich kann keine Privaten Nachrichten verschicken!',
    'Es gibt drei mögliche Gründe dafür: Sie sind nicht registriert bzw. eingeloggt, der Board Administrator hat das Private Nachrichten-System für das gesamte Board abgeschaltet oder der Administrator hat Ihnen das Schreiben von PMs untersagt. Falls das letzte zutreffen sollte, sollten Sie ihn fragen, warum.',
];
$faq[] = [
    'Ich erhalte dauernd ungewollte PMs!',
    'Es wird bald ein Ignorieren-System für das Private Nachrichten-System geben. Im Moment müssen Sie, falls Sie ununterbrochen unerwünschte Nachrichten von einer Person erhalten, den Administrator informieren. Er kann ein Versenden von PMs durch den jeweiligen Benutzer verbieten.',
];
$faq[] = [
    'Ich habe eine Spam- oder perverse E-Mail von jemandem auf diesem Board erhalten!',
    'Das E-Mail System dieses Boards enthält Sicherheitsvorkehrungen, um solche Aktionen eines Benutzers zu verhindern. Sie sollten dem Board Administrator eine Kopie der erhaltenen E-Mail schicken, wichtig dabei ist, dass die Kopfzeilen angefügt bleiben (die Details über den Benutzer, der die E-Mail schickte). Dann erst kann er handeln.',
];
//
// DIE DREI UNTEN STEHENDEN FRAGEN DER FAQ SOLLEN UNÜBERSETZT BLEIBEN, DA ES SICH UM INTERNATIONALES RECHT HANDELT - LASST DIE DREI EINTRÄGE BITTE ENGLISCH!
//
$faq[] = ['--', 'phpBB 2 Issues'];
$faq[] = [
    'Who wrote this bulletin board?',
    'This software (in its unmodified form) is produced, released and is copyright <a href="http://www.phpbb.com/" target="_blank">phpBB Group</a>. It is made available under the GNU General Public Licence and may be freely distributed, see link for more details',
];
$faq[] = [
    "Why isn't X feature available?",
    'This software was written by and licensed through phpBB Group. If you believe a feature needs to be added then please visit the phpbb.com website and see what phpBB Group have to say. Please do not post feature requests to the board at phpbb.com, the Group uses sourceforge to handle tasking of new features. Please read through the forums and see what, if any, our position may already be for a feature and then follow the procedure given there.',
];
$faq[] = [
    'Who do I contact about abusive and/or legal matters related to this board?',
    'You should contact the administrator of this board. If you cannot find who this you should first contact one of the forum moderators and ask them who you should in turn contact. If still get no response you should contact the owner of the domain (do a whois lookup) or, if this is running on a free service (e.g. yahoo, free.fr, f2s.com, etc.), the management or abuse department of that service. Please note that phpBB Group has absolutely no control and cannot in any way be held liable over how, where or by whom this board is used. It is absolutely pointless contacting phpBB Group in relation to any legal (cease and desist, liable, defamatory comment, etc.) matter not directly related to the phpbb.com website or the discrete software of phpBB itself. If you do email phpBB Group about any third party use of this software then you should expect a terse response or no response at all.',
];
//
// This ends the FAQ entries
//
