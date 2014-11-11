#Reflektioner

###Vad tror Du vi har för skäl till att spara det skrapade datat i JSON-format?
Jag tror att vi sparar det i JSON-format för att vi ska presentera datat som vi skrapar i textformat vilket JSON gör.

###Olika jämförelsesiter är flitiga användare av webbskrapor. Kan du komma på fler typer av tillämplingar där webbskrapor förekommer?
Jag tror att det förekommer mycket när man vill föra statistik av data på olika hemsidor eller bara analysera dem.
Det kan nog förekomma en he del skrapning av personer som vill komma åt personers e-post eller användarnamn på vissa
sidor.

###Hur har du i din skrapning underlättat för serverägaren?
Det jag har gjort för att underlätta lite för serverägaren är att jag när jag är färdig med skrapandet sparar ner datat
i en fil som fungerar som en cachning som gör att om jag laddar om sidan så får jag upp det senaste jag har skrapat utan
att göra några anrop till sidan.

###Vilka etiska aspekter bör man fundera kring vid webbskrapning?
Det man bör fundera kring är att man inte skrapar en sida om inte serverägaren har sagt att det är ok att göra och sedan
att man tar hänsyn till om det finns något som ägaren inte vill ska skrapas att man inte gör det.

###Vad finns det för risker med applikationer som innefattar automatisk skrapning av webbsidor? Nämn minst ett par stycken!
1. Att man får många anrop till servern som kan leda till att servern inte orkar med.
2. Att man kan åf tag på data som inte ägaren vill att man ska få tag på.
3. Om sidan uppdaterar HTML-strukturen så kan man inte skrapa sidan och få ut den information man vill utan måste då uppdatera
skrapan så att den följer den nya strukturen.

###Tänk dig att du skulle skrapa en sida gjord i ASP.NET WebForms. Vad för extra problem skulle man kunna få då?
När man skrapar en sida som är gjord i ASP.NET Webforms så måste man skicka med ViewStaten annars får man inte ut någon
information.

###Välj ut två punkter kring din kod du tycker är värd att diskutera vid redovisningen. Det kan röra val du gjort, tekniska lösningar eller lösningar du inte är riktigt nöjd med.
1. Går det att få mer strukturerat med olika filer och sådant, har allt i index i princip och det är inte så bra tycker inte jag.
2. Cachningen som jag letade upp på internet är en enkel lösning på hur man får en snabb och effektiv cachning.

###Hitta ett rättsfall som handlar om webbskrapning. Redogör kort för detta.
Det har varit ett rättsfall som innebar att man automatiskt placerade bud, som kallas auction sniping på eBay.

###Känner du att du lärt dig något av denna uppgift?
Jag har lärt mig ganska mycket av denna uppgift som jag inte kunde innan bland annat hur man letar in en DOM-struktur
men sedan har det också varit ganska jobbigt vissa gånger då sidan som man skrapar blir lite överbelastad vilket
resulterar i långa laddningstider av själva sidan och att skrapningen tar lång tid.
