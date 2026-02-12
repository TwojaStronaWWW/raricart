(function() {
  'use strict';

  // Translations
  const translations = {
    pl: {
        nav: { about: "O Nas", offer: "Oferta", gallery: "Galeria", faq: "FAQ", why_us: "Co Nas Wyróżnia", contact: "Kontakt" },
        hero: { title1: "TAM, GDZIE SMAK SPOTYKA EMOCJE, A PROSTOTA STAJE SIĘ ELEGANCJĄ...", title2: "...TAM ZACZYNA SIĘ <span class=\"logo-pulse\">RARICART</span>", scroll: "Przewiń w dół" },
        about: {
            title: "Nie tworzymy cateringu, lecz doświadczenie.",
            p1: "Tworzymy mobilne stacje degustacyjne, które stają się ozdobą każdego wydarzenia. To nie tylko jedzenie, to <span class=\"highlight\">subtelny, dopracowany i pełen charakteru</span> element scenografii.",
            p2: "Serwujemy lekkie, świeże kompozycje — od puszystych mini pancakes, przez autentyczne włoskie lody, aż po aromatyczne deski serów. Budujemy atmosferę klasy i swobody, w której Twoi goście poczują się wyjątkowo.",
            stats: {
                s1: { num: "100%", text: "Pasji i świeżości – każde danie przygotowujemy na oczach gości." },
                s2: { num: "3", text: "Unikalne stacje – Mini Pancakes, Lody Włoskie, Deski Serów (wyselekcjonowane i dopracowane)." },
                s3: { num: "0", text: "Kompromisów – używamy tylko składników premium i naturalnych produktów." },
                s4: { num: "∞", text: "Możliwości – personalizujemy menu i wygląd stacji pod Twój event." }
            }
        },
        offer: {
            title: "Nasza Oferta",
            intro_title: "NIE GOTUJEMY DAŃ, TWORZYMY CHWILE, KTÓRE ŁĄCZĄ LUDZI",
            p1: "Nasze stacje stają się miejscem rozmów, uśmiechów i&nbsp;zdjęć, a my dbamy o każdy szczegół, od montażu po ostatni serwis, abyś mógł cieszyć się wydarzeniem tak samo jak Twoi goście.",
            p2: "Obsługujemy eventy firmowe, wesela, gale<br> i&nbsp;prywatne przyjęcia, współpracując zarówno z agencjami, jak i&nbsp;klientami indywidualnymi.",
            p3: "W każdym projekcie kierujemy się zasadą, że smak i&nbsp;estetyka mają tę samą wartość, razem tworzą atmosferę, której nikt nie zapomina. Z Raricart zyskujesz nie tylko catering, ale spójny, piękny element scenografii Twojego wydarzenia, który smakuje tak dobrze, jak wygląda.",
            cards: {
                pancakes: { title: "Mini Pancakes", desc: "Słodka stacja, która angażuje gości i&nbsp;staje się sercem wydarzenia." },
                icecream: { title: "Lody Włoskie", desc: "Orzeźwiająca stacja, która zachwyca gości i&nbsp;buduje atmosferę." },
                cheese: { title: "Deska Serów", desc: "Fascynująca strefa smaku z&nbsp;włoskimi serami i&nbsp;wędlinami." }
            }
        },
        why: {
            title: "SZTUKA KULINARNYCH DOŚWIADCZEŃ",
            cards: {
                1: { title: "EFEKT „WOW\" Z KLASĄ", desc: "Na oczach gości serwujemy ciepłe pancakes prosto z patelni, nalewamy cremowe lody i komponujemy deski serów - wszystko świeże, personalizowane i dopracowane w detalach. Proces live station przyciąga uwagę, integruje uczestników i tworzy naturalne punkty spotkań, gdzie przy apetycznym widoku rodzą się rozmowy. Elegancki, mobilny design stacji podnosi prestiż wydarzenia, łącząc estetykę premium z czystą przyjemnością dla zmysłów." },
                2: { title: "MNIEJ LOGISTYKI, WIĘCEJ SPOKOJU", desc: "Raricart przejmuje całość: dojazd, montaż stacji, serwowanie podczas eventu, demontaż i perfekcyjny porządek po zakończeniu. Nie wymagamy zaplecza kuchennego - mobilne stacje działają wszędzie: w loftach, ogrodach, halach czy nietypowych przestrzeniach eventowych. Zespół synchronizuje serwis z harmonogramem, dba o płynny przepływ gości i minimalizuje kolejki." },
                3: { title: "DOŚWIADCZENIE ZAMIAST BUFETU", desc: "W odróżnieniu od statycznego bufetu, nasze live stations angażują: goście obserwują nalewanie lodów, układanie pancakes i komponowanie desek serów, wybierając dodatki na bieżąco. Wszystko serwowane porcjami „tu i teraz\" - świeże, bez marnowania, idealnie dopasowane do liczby i preferencji uczestników. Tematyczne stacje stają się magnesem na gości, budując emocje i niezapomniane wspomnienia." },
                4: { title: "BEZPIECZEŃSTWO, JAKOŚĆ, ESTETYKA", desc: "Przestrzegamy rygorystycznych standardów higieny i bezpieczeństwa żywności, z naciskiem na świeżość składników i perfekcyjną prezencję. Używamy wyselekcjonowanych produktów serwowanych w optymalnej temperaturze. Każdy detal - od aranżacji stacji, przez zastawę, po pracę zespołu - tworzy spójną scenografię, wzmacniającą wizerunek Twojego wydarzenia." },
                5: { title: "PARTNER DLA WYMAGAJĄCYCH", desc: "Agencje eventowe zyskują niezawodnego partnera rozumiejącego timing, layout i dynamikę dużych wydarzeń. Firmy, pary młode i organizatorzy prywatnych imprez otrzymują rozwiązanie premium: efekt „wow\", emocje i pełną opiekę nad gośćmi. Właściciele lokali eventowych wzbogacają ofertę o mobilne stacje bez inwestycji w sprzęt – gotowe do działania w dowolnej przestrzeni." },
                6: { title: "NAPISZ DO NAS", desc: "Twój event zasługuje na wyjątkowe live food station, które stanie się jego wizytówką. Napisz do nas już dziś - dopasujemy ofertę do Twojej wizji i zapewnimy termin. Razem stworzymy doświadczenie, które goście będą wspominać z zachwytem!" }
            }
        },
        faq: {
            title: "FAQ - Najczęściej Zadawane Pytania",
            q1: { title: "Czy jest ograniczona ilość porcji na osobę?", desc: "Nie, nie ma żadnych limitów! Goście mogą sięgać po świeże porcje ile tylko chcą. Nasze live food station to obfitość smaków przygotowywanych na żywo." },
            q2: { title: "Czy można przedłużyć czas trwania usługi?", desc: "Oczywiście! Elastyczność to nasza specjalność. Możesz przedłużyć usługę wcześniej, ustalając szczegóły, lub spontanicznie w trakcie eventu." },
            q3: { title: "W którym momencie wydarzenia najlepiej skorzystać ze stoiska Raricart?", desc: "Wybór należy do Ciebie - my idealnie się dopasujemy! Najczęściej stawiamy stoiska jako atrakcję na początek, podczas przerwy koktajlowej lub na deserowy finisz." },
            q4: { title: "Jak zarezerwować usługę Raricart?", desc: "To proste: skontaktuj się z nami przez formularz na stronie, e-mail lub telefon. Opowiedz o evencie, a w 24h prześlemy spersonalizowaną ofertę z menu i dostępnością. Rezerwacja z lekkim sercem!" },
            q5: { title: "Co jest potrzebne, by Raricart pojawiło się na Twoim evencie?", desc: "Tylko miejsce na nasze eleganckie stoisko (ok. 3x3m) i gniazdko prądu. Resztę załatwiamy my: dojazd, montaż, pełną obsługę, demontaż i sprzątanie. Zero zmartwień dla Ciebie." },
            q6: { title: "Jakie są ceny usług Raricart?", desc: "Ceny są elastyczne i zależą od menu, liczby gości oraz czasu trwania – od 150 zł/os. wzwyż dla premium live stations. Wyślij zapytanie, a przygotujemy transparentną wycenę." },
            q7: { title: "Czy obsługujecie eventy plenerowe i bez kuchni na miejscu?", desc: "Tak, jesteśmy mobilni na 100%! Dojedziemy wszędzie - na wesela w ogrodzie, firmowe pikniki czy gale pod chmurką. Bez zaplecza kuchennego? Żaden problem, nasze stoiska to kompletna, samodzielna magia kulinarna." },
            q8: { title: "Ile gości minimalnie obsługujecie?", desc: "Nie ma minimum – realizujemy zlecenia na każdą skalę! Od kameralnych imprez prywatnych (20+ osób) po duże eventy (500+). Dla mniejszych grup skalujemy jedno eleganckie stoisko z pełnym efektem \"wow\". Przy większych imprezach zalecamy więcej niż jedno stoisko – to poprawia jakość obsługi, skraca czas oczekiwania i minimalizuje kolejki." },
            q9: { title: "Jak zapewniacie higienę i bezpieczeństwo?", desc: "Jesteśmy certyfikowani (HACCP, Sanepid), z pełnym protokołem higieny na żywo. Świeże składniki, sterylne narzędzia i doświadczona obsługa." }
        },
        contact: { title: "Zapytaj o dostępność terminu<br>i stwórzmy razem strefę smaku,<br>o której Twoi goście długo nie zapomną." },
        form: {
            name: "Imię i Nazwisko *", email: "Email *", phone: "Telefon *", date: "Data Wydarzenia *", 
            guests_label: "Liczba Gości *", budget: "Budżet *", event_type: "Rodzaj Wydarzenia *",
            select_placeholder: "Wybierz...",
            types: { wedding: "Wesele", corporate: "Event Firmowy", festival: "Festiwal/Piknik", private: "Przyjęcie Prywatne", other: "Inne" },
            stations: "Interesujące Stacje *",
            st_pancakes: "Mini Pancakes", st_icecream: "Lody Włoskie", st_cheese: "Deska Serów",
            message: "Dodatkowe Informacje", submit: "Wyślij Zapytanie",
            required: "To pole jest wymagane",
            sending: "Wysyłanie...",
            success_msg: "Szczegóły zapytania zostały przesłane. Potwierdzamy przyjęcie wiadomości. Skontaktujemy się z Państwem wkrótce w celu omówienia szczegółów.",
            error_msg: "Błąd wysyłania. Sprawdź połączenie lub spróbuj później.",
            stations_error: "Wybierz przynajmniej jedną stację",
            progress_text: "Uzupełnij dane, abyśmy mogli przygotować ofertę (0%)",
            message_placeholder: "Opisz swoje potrzeby, pytania lub preferencje..."
        },
        cookies: {
            text: "Ta strona używa plików cookies, aby zapewnić najlepszą jakość. Korzystając ze strony, zgadzasz się na ich użycie.",
            accept: "Akceptuję",
            reject: "Odrzuć"
        },
        footer: {
            desc: "Mobilne stacje degustacyjne na eventy w całej Polsce.",
            phone: "Telefon: <a href=\"tel:+48883392688\" class=\"phone-link\">+48 883 392 688</a>",
            quick_links: "Szybkie Linki"
        },
        gallery: { title: "GALERIA REALIZACJI" },
        modals: {
            pancakes: {
                title: 'Mini Pancakes',
                content: `<p class="modal-lead">Słodka stacja, która angażuje gości, przyciąga uwagę i&nbsp;naturalnie staje się jednym z&nbsp;najbardziej lubianych punktów wydarzenia.</p>
        <div class="modal-section"><h4>JAK TO DZIAŁA?</h4><p>Na oczach gości powstają delikatne, złociste pancakes: lekkie, puszyste i&nbsp;podawane w&nbsp;eleganckiej formie. Każda porcja przygotowywana jest na świeżo, dzięki czemu przestrzeń wypełnia przyjemny aromat, który natychmiast przyciąga uwagę.</p><p>Goście mogą samodzielnie stworzyć swoją kompozycję, wybierając spośród dodatków takich jak owoce, czekolada, posypki, polewy czy chrupiące ciasteczka. To moment swobody i&nbsp;kreatywności, który sprawia, że degustacja staje się przyjemnym doświadczeniem, a&nbsp;nie tylko deserem.</p></div>
        <div class="modal-section"><h4>DOBÓR DODATKÓW</h4><p>Jeśli wolisz, przejmiemy tę część za Ciebie: przygotujemy zestaw dodatków idealnie dopasowany do stylu wydarzenia i&nbsp;profilu gości. Zadbamy o&nbsp;harmonię smaków i&nbsp;estetykę prezentacji, tak by całość była spójna z&nbsp;charakterem wydarzenia i&nbsp;jego atmosferą.</p><p>Możesz także samodzielnie wybrać dodatki z&nbsp;naszej listy: damy Ci pełną swobodę w&nbsp;komponowaniu oferty według własnych preferencji.</p></div>
        <div class="modal-section"><h4>GDZIE SPRAWDZA SIĘ NASZA STACJA?</h4><ul><li>Eventy firmowe i&nbsp;konferencje</li><li>Wesela i&nbsp;przyjęcia weselne</li><li>Gale i&nbsp;bankiety</li><li>Prywatne przyjęcia i&nbsp;urodziny</li><li>Imprezy plenerowe i&nbsp;pikniki</li></ul></div>
        <div class="modal-section"><h4>PRZYKŁADOWE MENU</h4><ul><li><strong>Classic:</strong> Cukier puder / Syrop klonowy</li><li><strong>Sweet Dream:</strong> Nutella / Banan / Kruszonka</li><li><strong>Fruity Fresh:</strong> Biała Czekolada / Świeże Maliny</li><li><strong>Crunchy:</strong> Solony Karmel / Orzeszki Ziemne</li></ul></div>
        <div class="modal-section"><h4>CO OTRZYMUJESZ?</h4><ul><li>Profesjonalną, mobilną stację o&nbsp;dopracowanej estetyce</li><li>Obsługę od montażu po ostatni serwis</li><li>Świeżo przygotowane pancakes i&nbsp;starannie dobrane dodatki</li><li>Punkt, który angażuje gości i&nbsp;tworzy naturalne miejsce spotkań</li></ul></div>`,

            },
            icecream: {
                title: 'Lody Włoskie',
                content: `<p class="modal-lead">Orzeźwiająca stacja, która zachwyca gości, buduje atmosferę i&nbsp;naturalnie staje się hitem wydarzenia.</p>
        <div class="modal-section"><h4>JAK TO DZIAŁA?</h4><p>Goście mają okazję zobaczyć, jak kremowe lody włoskie nalewane są prosto z&nbsp;maszyny do eleganckich kubeczków: z&nbsp;aksamitną konsystencją i&nbsp;idealną świeżością. To proste, widowiskowe przedstawienie działa na zmysły i&nbsp;sprawia, że każdy deser jest wyjątkowy. Porcje serwowane są na bieżąco, zarówno na eventy plenerowe, jak i&nbsp;w&nbsp;przestrzeniach zamkniętych.</p><p>Do lodów dobieramy dodatki takie jak świeże owoce, sosy owocowe i&nbsp;czekoladowe, chrupiące posypki, orzechy czy mini ciasteczka, umożliwiając gościom stworzenie własnych kompozycji smakowych.</p></div>
        <div class="modal-section"><h4>DOBÓR SMAKÓW I&nbsp;DODATKÓW</h4><p>Możesz powierzyć nam dobór smaków i&nbsp;dodatków: dopasujemy konfigurację do charakteru wydarzenia, sezonu i&nbsp;profilu gości. Możliwa jest też pełna swoboda w&nbsp;samodzielnym skomponowaniu listy, tak by oferta idealnie wpasowała się w&nbsp;Twoją koncepcję.</p></div>
        <div class="modal-section"><h4>GDZIE SPRAWDZA SIĘ STACJA LODÓW?</h4><ul><li>Eventy firmowe i&nbsp;dni otwarte</li><li>Wesela, poprawiny i&nbsp;letnie przyjęcia</li><li>Gale, premiery, eventy wizerunkowe</li><li>Przyjęcia rodzinne, urodziny, komunie</li><li>Imprezy plenerowe i&nbsp;pikniki</li></ul></div>
        <div class="modal-section"><h4>PRZYKŁADOWE MENU (Smaki)</h4><ul><li><strong>Pistacja Sycylijska:</strong> 100% pasty pistacjowej</li><li><strong>Belgijska Czekolada:</strong> Intensywna i kremowa</li><li><strong>Śmietanka z Wanilią:</strong> Klasyka w najlepszym wydaniu</li><li><strong>Sorbet Mango-Marakuja:</strong> Orzeźwienie z nutą orientu</li></ul></div>
        <div class="modal-section"><h4>CO ZYSKUJESZ?</h4><ul><li>Estetyczną, mobilną stację lodów dopasowaną do charakteru wydarzenia</li><li>Kompleksową obsługę – od przygotowania po serwis w&nbsp;trakcie eventu</li><li>Kremowe lody włoskie serwowane na żywo z&nbsp;profesjonalnie dobranymi dodatkami</li><li>Punkt, który naturalnie przyciąga gości i&nbsp;buduje pozytywne skojarzenia z&nbsp;wydarzeniem.</li></ul></div>`,
            },
            cheese: {
                title: 'Deska Serów',
                content: `<p class="modal-lead">Fascynująca strefa smaku, która podnosi wartość Twojego wydarzenia.</p>
        <div class="modal-section"><h4>JAK TO DZIAŁA?</h4><p>Nasze stoisko oferuje 12 starannie wyselekcjonowanych pozycji: wysokiej jakości sery i&nbsp;wędliny włoskie, uzupełnione wybornymi dodatkami słonymi. Wszystko to komponuje się w&nbsp;harmonijną całość, wzbogaconą o&nbsp;specjalnie dobrane sosy, które wzmacniają smak każdej degustacji. Goście sami tworzą swoje własne mini kompozycje desek serów, swobodnie sięgając po ulubione składniki: od kremowych serów z&nbsp;dojrzewającymi wędlinami, przez chrupiące dodatki, po wyrafinowane sosy podkreślające włoski charakter całości.</p></div>
        <div class="modal-section"><h4>STARANNIE SKOMPONOWANY DOBÓR SKŁADNIKÓW</h4><p>Dobór wszystkich 12 pozycji i&nbsp;sosów jest precyzyjnie przemyślany: każdy składnik został wyselekcjonowany tak, aby idealnie komponował się z&nbsp;pozostałymi, tworząc harmonijną całość smakową i&nbsp;wizualną. Kompozycja została stworzona z&nbsp;myślą o&nbsp;najwyższych standardach doświadczeń degustacyjnych, gwarantując gościom profesjonalne wrażenia na światowym poziomie. Powierzenie nam tego aspektu pozwala skupić się na organizacji wydarzenia, podczas gdy my zapewniamy spójność i&nbsp;jakość każdej porcji.</p></div>
        <div class="modal-section"><h4>GDZIE SPRAWDZA SIĘ STOISKO SERÓW?</h4><ul><li>Eventy firmowe, bankiety i&nbsp;koktajle</li><li>Wesela, przyjęcia przedślubne i&nbsp;poprawiny</li><li>Gale, wernisaże, premiery produktowe</li><li>Kameralne przyjęcia prywatne i&nbsp;spotkania biznesowe</li><li>Konferencje i&nbsp;networkingowe spotkania</li></ul></div>
        <div class="modal-section"><h4>GDZIE SPRAWDZA SIĘ STACJA SERÓW?</h4><ul><li>Koktajle biznesowe i&nbsp;wernisaże</li><li>Strefy chillout na weselach</li><li>Ekskluzywne bankiety</li><li>Degustacje win</li></ul></div>
        <div class="modal-section"><h4>PRZYKŁADOWE MENU</h4><ul><li><strong>Sery:</strong> Parmigiano Reggiano 24m, Gorgonzola Dolce, Pecorino Romano</li><li><strong>Wędliny:</strong> Prosciutto di Parma, Spianata Piccante, Salami Milano</li><li><strong>Dodatki:</strong> Konfitura z fig, oliwki Liguryjskie, świeże winogrona, orzechy włoskie, krakersy rzemieślnicze</li></ul></div>
        <div class="modal-section"><h4>CO ZYSKUJESZ?</h4><ul><li>Spektakularną ekspozycję kulinarną, która przyciąga wzrok</li><li>Wysokiej jakości produkty, które zadowolą koneserów</li><li>Alternatywę dla klasycznego bufetu – "grazing table" w&nbsp;wersji premium</li><li>Elegancki element, który podnosi rangę wydarzenia</li></ul></div>`,
            }
        }
    },
    en: {
        nav: { about: "About Us", offer: "Offer", gallery: "Gallery", faq: "FAQ", why_us: "Why Us", contact: "Contact" },
        hero: { title1: "WHERE TASTE MEETS EMOTION AND SIMPLICITY BECOMES ELEGANCE...", title2: "...THAT'S WHERE <span class=\"logo-pulse\">RARICART</span> BEGINS", scroll: "Scroll Down" },
        about: {
            title: "We don't create catering, but an experience.",
            p1: "We create mobile tasting stations that become the highlight of every event. It's not just food, it's a <span class=\"highlight\">subtle, refined, and full of character</span> scenic element.",
            p2: "We serve light, fresh compositions — from fluffy mini pancakes, through authentic Italian ice cream, to aromatic cheese boards. We build an atmosphere of class and freedom where your guests feel special."
        },
        offer: {
            title: "Our Offer",
            intro_title: "WE DON'T JUST COOK, WE CREATE MOMENTS THAT CONNECT PEOPLE",
            p1: "Our stations become places for conversation, smiles, and photos, while we take care of every detail, from setup to the last service.",
            p2: "We serve corporate events, weddings, galas,<br> and private parties, working with both agencies and individual clients.",
            p3: "In every project, we believe that taste and aesthetics have equal value—together they create an unforgettable atmosphere. With Raricart, you get a cohesive, beautiful scenic element that tastes as good as it looks.",
            cards: {
                pancakes: { title: "Mini Pancakes", desc: "A sweet station that engages guests and becomes the heart of the event." },
                icecream: { title: "Soft Serve Ice Cream", desc: "A refreshing station that delights guests and builds atmosphere." },
                cheese: { title: "Cheese Board", desc: "A fascinating taste zone with Italian cheeses and cold cuts." }
            }
        },
        why: {
            title: "THE ART OF CULINARY EXPERIENCES",
            cards: {
                1: { title: "CLASSY 'WOW' EFFECT", desc: "We serve warm pancakes right from the griddle, pour creamy ice cream, and compose cheese boards right before guests' eyes - everything fresh, personalized, and refined in detail. The live station process attracts attention, integrates participants, and creates natural meeting points where conversations are born over an appetizing view. The elegant, mobile station design raises the prestige of the event, combining premium aesthetics with pure pleasure for the senses." },
                2: { title: "LESS LOGISTICS, MORE PEACE", desc: "Raricart takes over everything: travel, station assembly, service during the event, disassembly, and perfect cleanup afterwards. We don't need kitchen facilities - our mobile stations work everywhere: in lofts, gardens, halls, or unusual event spaces. The team synchronizes the service with the schedule, ensures smooth guest flow, and minimizes queues." },
                3: { title: "EXPERIENCE INSTEAD OF BUFFET", desc: "Unlike a static buffet, our live stations engage: guests watch the pouring of ice cream, stacking of pancakes, and composing of cheese boards, choosing toppings on the fly. Everything is served in portions 'here and now' - fresh, without waste, perfectly matched to the number and preferences of participants. Themed stations become a magnet for guests, building emotions and unforgettable memories." },
                4: { title: "SAFETY, QUALITY, AESTHETICS", desc: "We adhere to strict hygiene and food safety standards, with an emphasis on ingredient freshness and perfect presentation. We use selected products served at optimal temperatures. Every detail - from station arrangement, through tableware, to team work - creates a cohesive scenography that strengthens your event's image." },
                5: { title: "PARTNER FOR THE DEMANDING", desc: "Event agencies gain a reliable partner who understands timing, layout, and the dynamics of large events. Companies, couples, and private party organizers receive a premium solution: a 'wow' effect, emotions, and full care for guests. Event venue owners enrich their offer with mobile stations without investing in equipment – ready to operate in any space." },
                6: { title: "WRITE TO US", desc: "Your event deserves a unique live food station that will become its showcase. Write to us today - we will tailor the offer to your vision and secure the date. Together we will create an experience that guests will remember with delight!" }
            }
        },
        faq: {
            title: "FAQ - Frequently Asked Questions",
            q1: { title: "Is there a limit on portions per person?", desc: "No, there are no limits! Guests can reach for fresh portions as much as they want. Our live food station is an abundance of flavors prepared live." },
            q2: { title: "Can the service duration be extended?", desc: "Of course! Flexibility is our specialty. You can extend the service in advance by arranging details, or spontaneously during the event." },
            q3: { title: "At what moment of the event is it best to use the Raricart stand?", desc: "The choice is yours - we will fit in perfectly! We most often set up stands as an attraction at the beginning, during a cocktail break, or as a dessert finish." },
            q4: { title: "How to book Raricart service?", desc: "It's simple: contact us via the form on the website, email, or phone. Tell us about the event, and within 24h we will send a personalized offer with menu and availability. Booking with a light heart!" },
            q5: { title: "What is needed for Raricart to appear at your event?", desc: "Only space for our elegant stand (approx. 3x3m) and a power outlet. We handle the rest: transport, assembly, full service, disassembly, and cleaning. Zero worries for you." },
            q6: { title: "What are the prices of Raricart services?", desc: "Prices are flexible and depend on the menu, number of guests, and duration – from 150 PLN/person upwards for premium live stations. Send an inquiry, and we will prepare a transparent quote." },
            q7: { title: "Do you serve outdoor events and those without a kitchen on site?", desc: "Yes, we are 100% mobile! We will get anywhere - to garden weddings, corporate picnics, or open-air galas. No kitchen facilities? No problem, our stands are complete, independent culinary magic." },
            q8: { title: "What is the minimum number of guests you serve?", desc: "There is no minimum – we carry out orders on any scale! From intimate private parties (20+ people) to large events (500+). For smaller groups, we scale one elegant stand with a full \"wow\" effect. For larger events, we recommend more than one stand – this improves service quality, shortens waiting time, and minimizes queues." },
            q9: { title: "How do you ensure hygiene and safety?", desc: "We are certified (HACCP, Sanepid), with a full live hygiene protocol. Fresh ingredients, sterile tools, and experienced service." }
        },
        contact: { title: "Ask for availability<br>and let's create a taste zone together<br>that your guests will not forget." },
        form: {
            name: "Name & Surname *", email: "Email *", phone: "Phone *", date: "Event Date *", 
            guests_label: "Number of Guests *", budget: "Budget *", event_type: "Event Type *",
            select_placeholder: "Choose...",
            types: { wedding: "Wedding", corporate: "Corporate Event", festival: "Festival/Picnic", private: "Private Party", other: "Other" },
            stations: "Interested Stations *",
            st_pancakes: "Mini Pancakes", st_icecream: "Soft Serve Ice Cream", st_cheese: "Cheese Board",
            message: "Additional Information", submit: "Send Query",
            required: "This field is required",
            stations_error: "Select at least one station",
            sending: "Sending...",
            success_msg: "Inquiry details have been sent. We confirm receipt of the message. We will contact you shortly to discuss details.",
            error_msg: "Error sending. Check your connection or try again later.",
            progress_text: "Complete the data so we can prepare an offer (0%)",
            message_placeholder: "Describe your needs, questions or preferences..."
        },
        cookies: {
            text: "This site uses cookies to ensure the best quality. By using the site, you agree to their use.",
            accept: "Accept",
            reject: "Reject"
        },
        footer: {
            desc: "Mobile tasting stations for events all over Poland.",
            phone: "Phone: <a href=\"tel:+48883392688\" class=\"phone-link\">+48 883 392 688</a>",
            quick_links: "Quick Links"
        },
        gallery: { title: "GALLERY" },
        modals: {
            pancakes: {
                title: 'Mini Pancakes',
                content: `<p class="modal-lead">A sweet station that engages guests, attracts attention, and naturally becomes one of the most beloved points of the event.</p>
        <div class="modal-section"><h4>HOW IT WORKS?</h4><p>Delicate, golden pancakes are created before guests' eyes: light, fluffy, and served elegantly. Each portion is prepared fresh, filling the space with a pleasant aroma that immediately draws attention.</p><p>Guests can create their own compositions, choosing from toppings such as fruits, chocolate, sprinkles, sauces, or crispy cookies. It's a moment of freedom and creativity that makes tasting a pleasant experience, not just a dessert.</p></div>
        <div class="modal-section"><h4>TOPPING SELECTION</h4><p>If you prefer, we'll take care of this part for you: we'll prepare a set of toppings perfectly matched to the event style and guest profile. We'll ensure harmony of flavors and aesthetic presentation, so that the whole is consistent with the event's character and atmosphere.</p><p>You can also choose toppings from our list yourself: we give you full freedom to compose the offer according to your own preferences.</p></div>
        <div class="modal-section"><h4>WHERE DOES OUR STATION WORK?</h4><ul><li>Corporate events and conferences</li><li>Weddings and wedding receptions</li><li>Galas and banquets</li><li>Private parties and birthdays</li><li>Outdoor events and picnics</li></ul></div>
        <div class="modal-section"><h4>WHAT DO YOU GET?</h4><ul><li>Professional, mobile station with refined aesthetics</li><li>Service from setup to the last serving</li><li>Freshly prepared pancakes and carefully selected toppings</li><li>A point that engages guests and creates a natural meeting place</li></ul></div>`,
            },
            icecream: {
                title: 'Soft Serve',
                content: `<p class="modal-lead">A refreshing station that delights guests, builds atmosphere, and naturally becomes an event hit.</p>
        <div class="modal-section"><h4>HOW IT WORKS?</h4><p>Guests have the opportunity to see creamy Italian ice cream poured directly from the machine into elegant cups: with a velvety consistency and ideal freshness. This simple, spectacular presentation appeals to the senses and makes every dessert unique. Portions are served continuously, both for outdoor events and in enclosed spaces.</p><p>We select toppings for ice cream such as fresh fruits, fruit and chocolate sauces, crispy sprinkles, nuts, or mini cookies, allowing guests to create their own flavor compositions.</p></div>
        <div class="modal-section"><h4>FLAVOR AND TOPPING SELECTION</h4><p>You can entrust us with the selection of flavors and toppings: we will adapt the configuration to the character of the event, season, and guest profile. Full freedom is also possible in composing the list yourself, so that the offer perfectly fits your concept.</p></div>
        <div class="modal-section"><h4>WHERE DOES THE ICE CREAM STATION WORK?</h4><ul><li>Corporate events and open days</li><li>Weddings, after-parties, and summer receptions</li><li>Galas, premieres, image events</li><li>Family parties, birthdays, communions</li><li>Outdoor events and picnics</li></ul></div>
        <div class="modal-section"><h4>WHAT DO YOU GAIN?</h4><ul><li>Aesthetic, mobile ice cream station adapted to the event's character</li><li>Comprehensive service – from preparation to service during the event</li><li>Creamy Italian ice cream served live with professionally selected toppings</li><li>A point that naturally attracts guests and builds positive associations with the event.</li></ul></div>`,
            },
            cheese: {
                title: 'Cheese Board',
                content: `<p class="modal-lead">A fascinating taste zone that enhances the value of your event.</p>
        <div class="modal-section"><h4>HOW IT WORKS?</h4><p>Our stand offers 12 carefully selected items: high-quality Italian cheeses and cold cuts, complemented by exquisite savory additions. All this combines into a harmonious whole, enriched with specially selected sauces that enhance the taste of each tasting. Guests create their own mini cheese board compositions, freely reaching for their favorite ingredients: from creamy cheeses with aged cold cuts, through crispy additions, to refined sauces emphasizing the Italian character of the whole.</p></div>
        <div class="modal-section"><h4>CAREFULLY COMPOSED SELECTION OF INGREDIENTS</h4><p>The selection of all 12 items and sauces is precisely thought out: each ingredient has been selected to perfectly complement the others, creating a harmonious taste and visual whole. The composition was created with the highest standards of tasting experiences in mind, guaranteeing guests professional, world-class impressions. Entrusting us with this aspect allows you to focus on organizing the event, while we ensure the consistency and quality of each portion.</p></div>
        <div class="modal-section"><h4>WHERE DOES THE CHEESE STAND WORK?</h4><ul><li>Corporate events, banquets, and cocktails</li><li>Weddings, pre-wedding parties, and after-parties</li><li>Galas, vernissages, product launches</li><li>Intimate private parties and business meetings</li><li>Conferences and networking meetings</li></ul></div>
        <div class="modal-section"><h4>WHAT DO YOU GAIN?</h4><ul><li>Aesthetic, mobile stand with top-quality Italian cheeses and cold cuts</li><li>Comprehensive service: from arrangement to service and replenishment during the event</li><li>12 precisely selected items plus sauces enhancing the tasting</li><li>A point that naturally attracts guests and builds positive associations with the event.</li></ul></div>`,
            }
        }
    },
    es: {
        nav: { about: "Sobre Nosotros", offer: "Oferta", gallery: "Galería", faq: "FAQ", why_us: "Por Qué Nosotros", contact: "Contacto" },
        hero: { title1: "DONDE EL SABOR SE ENCUENTRA CON LA EMOCIÓN Y LA SIMPLICIDAD SE VUELVE ELEGANCIA...", title2: "...AHÍ ES DONDE COMIENZA RARICART", scroll: "Desplácese hacia abajo" },
        about: {
            title: "NO CREAMOS CATERING, SINO UNA EXPERIENCIA QUE DELEITA LOS OJOS, INVOLUCRA LOS SENTIDOS Y PERMANECE EN LA MEMORIA POR MUCHO TIEMPO",
            p1: "Creamos estaciones de degustación móviles que se convierten en el punto culminante de cada evento: sutiles, refinadas y llenas de carácter.",
            p2: "Servimos composiciones ligeras y frescas, desde mini pancakes <br>y helados italianos hasta aromáticas tablas de quesos, creando una atmósfera donde los invitados se sienten relajados y especiales."
        },
        offer: {
            title: "Nuestra Oferta",
            intro_title: "NO SOLO COCINAMOS, CREAMOS MOMENTOS QUE CONECTAN A LAS PERSONAS",
            p1: "Nuestras estaciones se convierten en lugares para conversar, sonreír y tomar fotos, mientras nosotros nos ocupamos de cada detalle, desde el montaje hasta el último servicio, para que puedas disfrutar del evento tanto como tus invitados.",
            p2: "Atendemos eventos corporativos, bodas, galas<br> y fiestas privadas, trabajando tanto con agencias como con clientes individuales.",
            p3: "En cada proyecto, nos guiamos por el principio de que el sabor y la estética tienen el mismo valor: juntos crean una atmósfera inolvidable. Con Raricart obtienes no solo catering, sino un elemento escénico coherente y hermoso de tu evento, que sabe tan bien como se ve.",
            cards: {
                pancakes: { title: "Mini Pancakes", desc: "Una estación dulce que atrae a los invitados y se convierte en el corazón del evento." },
                icecream: { title: "Helado Suave", desc: "Una estación refrescante que deleita a los invitados y crea ambiente." },
                cheese: { title: "Tabla de Quesos", desc: "Una fascinante zona de sabor con quesos y embutidos italianos." }
            }
        },
        why: {
            title: "EL ARTE DE LAS EXPERIENCIAS CULINARIAS",
            cards: {
                1: { title: "EFECTO 'WOW' CON CLASE", desc: "Servimos pancakes calientes directamente de la plancha, vertemos helado cremoso y componemos tablas de quesos ante los ojos de los invitados: todo fresco, personalizado y refinado en cada detalle. El proceso de estación en vivo atrae la atención, integra a los participantes y crea puntos de encuentro naturales, donde nacen conversaciones ante una vista apetecible. El diseño elegante y móvil de la estación eleva el prestigio del evento, combinando estética premium con puro placer para los sentidos." },
                2: { title: "MENOS LOGÍSTICA, MÁS PAZ", desc: "Raricart se encarga de todo: viaje, montaje de la estación, servicio durante el evento, desmontaje y limpieza perfecta después. No necesitamos instalaciones de cocina: nuestras estaciones móviles funcionan en todas partes: en lofts, jardines, salones o espacios para eventos inusuales. El equipo sincroniza el servicio con el horario, asegura un flujo fluido de invitados y minimiza las colas." },
                3: { title: "EXPERIENCIA EN LUGAR DE BUFFET", desc: "A diferencia de un buffet estático, nuestras estaciones en vivo involucran: los invitados observan cómo se sirve el helado, se apilan los pancakes y se componen las tablas de quesos, eligiendo aderezos sobre la marcha. Todo se sirve en porciones 'aquí y ahora': fresco, sin desperdicios, perfectamente adaptado al número y preferencias de los participantes. Las estaciones temáticas se convierten en un imán para los invitados, construyendo emociones y recuerdos inolvidables." },
                4: { title: "SEGURIDAD, CALIDAD, ESTÉTICA", desc: "Cumplimos con estrictos estándares de higiene y seguridad alimentaria, con énfasis en la frescura de los ingredientes y una presentación perfecta. Utilizamos productos seleccionados servidos a temperaturas óptimas. Cada detalle, desde la disposición de la estación, pasando por la vajilla, hasta el trabajo del equipo, crea una escenografía coherente que fortalece la imagen de tu evento." },
                5: { title: "SOCIO PARA LOS EXIGENTES", desc: "Las agencias de eventos obtienen un socio confiable que comprende los tiempos, el diseño y la dinámica de los grandes eventos. Empresas, parejas y organizadores de fiestas privadas reciben una solución premium: efecto 'wow', emociones y cuidado total de los invitados. Los propietarios de locales para eventos enriquecen su oferta con estaciones móviles sin invertir en equipos, listos para operar en cualquier espacio." },
                6: { title: "ESCRIBENOS", desc: "Tu evento merece una estación de comida en vivo única que se convierta en su escaparate. Escríbenos hoy: adaptaremos la oferta a tu visión y aseguraremos la fecha. ¡Juntos crearemos una experiencia que los invitados recordarán con deleite!" }
            }
        },
        faq: {
            title: "FAQ - Preguntas Frecuentes",
            q1: { title: "¿Hay un límite de porciones por persona?", desc: "¡No, no hay límites! Los invitados pueden servirse porciones frescas tanto como quieran. Nuestra estación de comida en vivo es una abundancia de sabores preparados al momento." },
            q2: { title: "¿Se puede extender la duración del servicio?", desc: "¡Por supuesto! La flexibilidad es nuestra especialidad. Puede extender el servicio con anticipación acordando los detalles, o espontáneamente durante el evento." },
            q3: { title: "¿En qué momento del evento es mejor utilizar el puesto de Raricart?", desc: "La elección es suya: ¡nos adaptaremos perfectamente! Con mayor frecuencia instalamos puestos como atracción al principio, durante un cóctel o como broche final de postre." },
            q4: { title: "¿Cómo reservar el servicio de Raricart?", desc: "Es simple: contáctenos a través del formulario en el sitio web, correo electrónico o teléfono. Cuéntenos sobre el evento y en 24h le enviaremos una oferta personalizada con menú y disponibilidad. ¡Reserva con el corazón ligero!" },
            q5: { title: "¿Qué se necesita para que Raricart aparezca en su evento?", desc: "Solo espacio para nuestro elegante puesto (aprox. 3x3m) y una toma de corriente. Nosotros nos encargamos del resto: transporte, montaje, servicio completo, desmontaje y limpieza. Cero preocupaciones para usted." },
            q6: { title: "¿Cuáles son los precios de los servicios de Raricart?", desc: "Los precios son flexibles y dependen del menú, el número de invitados y la duración: desde 150 PLN/persona en adelante para estaciones en vivo premium. Envíe una consulta y prepararemos un presupuesto transparente." },
            q7: { title: "¿Atienden eventos al aire libre y sin cocina en el lugar?", desc: "¡Sí, somos 100% móviles! Llegaremos a cualquier lugar: bodas en el jardín, picnics corporativos o galas al aire libre. ¿Sin instalaciones de cocina? No hay problema, nuestros puestos son magia culinaria completa e independiente." },
            q8: { title: "¿Cuál es el número mínimo de invitados que atienden?", desc: "No hay mínimo: ¡realizamos pedidos a cualquier escala! Desde fiestas privadas íntimas (20+ personas) hasta grandes eventos (500+). Para grupos más pequeños, escalamos un puesto elegante con un efecto \"wow\" completo. Para eventos más grandes, recomendamos más de un puesto: esto mejora la calidad del servicio, acorta el tiempo de espera y minimiza las colas." },
            q9: { title: "¿Cómo garantizan la higiene y la seguridad?", desc: "Estamos certificados (HACCP, Sanepid), con un protocolo completo de higiene en vivo. Ingredientes frescos, herramientas estériles y servicio experimentado." }
        },
        contact: { title: "Pregunte por disponibilidad<br>y creemos juntos una zona de sabor<br>que sus invitados no olvidarán." },
        form: {
            name: "Nombre y Apellidos *", email: "Email *", phone: "Teléfono *", date: "Fecha del Evento *", 
            guests_label: "Número de Invitados *", budget: "Presupuesto *", event_type: "Tipo de Evento *",
            select_placeholder: "Seleccionar...",
            types: { wedding: "Boda", corporate: "Evento Corporativo", festival: "Festival/Picnic", private: "Fiesta Privada", other: "Otro" },
            stations: "Estaciones de Interés *",
            st_pancakes: "Mini Pancakes", st_icecream: "Helado Suave", st_cheese: "Tabla de Quesos",
            message: "Información Adicional", submit: "Enviar Consulta",
            progress_text: "Complete los datos para que podamos preparar una oferta (0%)",
            message_placeholder: "Describa sus necesidades, preguntas o preferencias..."
        },
        cookies: {
            text: "Este sitio utiliza cookies para garantizar la mejor calidad. Al utilizar el sitio, usted acepta su uso.",
            accept: "Aceptar",
            reject: "Rechazar"
        },
        footer: {
            desc: "Estaciones de degustación móviles para eventos en toda Polonia.",
            phone: "Teléfono: <a href=\"tel:+48883392688\" class=\"phone-link\">+48 883 392 688</a>",
            quick_links: "Enlaces Rápidos"
        },
        gallery: { title: "GALERÍA" },
        modals: {
            pancakes: {
                title: 'Mini Pancakes',
                content: `<p class="modal-lead">Una estación dulce que atrae a los invitados, capta la atención y, naturalmente, se convierte en uno de los puntos más queridos del evento.</p>
        <div class="modal-section"><h4>¿CÓMO FUNCIONA?</h4><p>Delicados y dorados pancakes se crean ante los ojos de los invitados: ligeros, esponjosos y servidos de forma elegante. Cada porción se prepara al momento, llenando el espacio con un agradable aroma que atrae inmediatamente la atención.</p><p>Los invitados pueden crear su propia composición, eligiendo entre aderezos como frutas, chocolate, chispas, salsas o galletas crujientes. Es un momento de libertad y creatividad que convierte la degustación en una experiencia placentera, no solo un postre.</p></div>
        <div class="modal-section"><h4>SELECCIÓN DE ADEREZOS</h4><p>Si lo prefieres, nosotros nos encargamos de esta parte: prepararemos un conjunto de aderezos perfectamente adaptados al estilo del evento y al perfil de los invitados. Nos aseguraremos de la armonía de sabores y la presentación estética, para que todo sea coherente con el carácter y la atmósfera del evento.</p><p>También puedes elegir los aderezos de nuestra lista: te damos total libertad para componer la oferta según tus propias preferencias.</p></div>
        <div class="modal-section"><h4>¿DÓNDE FUNCIONA NUESTRA ESTACIÓN?</h4><ul><li>Eventos corporativos y conferencias</li><li>Bodas y recepciones de boda</li><li>Galas y banquetes</li><li>Fiestas privadas y cumpleaños</li><li>Eventos al aire libre y picnics</li></ul></div>
        <div class="modal-section"><h4>¿QUÉ OBTIENES?</h4><ul><li>Estación profesional y móvil con una estética refinada</li><li>Servicio desde el montaje hasta el último servicio</li><li>Pancakes recién preparados y aderezos cuidadosamente seleccionados</li><li>Un punto que atrae a los invitados y crea un lugar de encuentro natural</li></ul></div>`,
            },
            icecream: {
                title: 'Helado Suave',
                content: `<p class="modal-lead">Una estación refrescante que deleita a los invitados, crea ambiente y, naturalmente, se convierte en un éxito del evento.</p>
        <div class="modal-section"><h4>¿CÓMO FUNCIONA?</h4><p>Los invitados tienen la oportunidad de ver cómo se vierte cremoso helado italiano directamente de la máquina en elegantes vasos: con una consistencia aterciopelada y una frescura ideal. Esta presentación sencilla y espectacular apela a los sentidos y hace que cada postre sea único. Las porciones se sirven continuamente, tanto para eventos al aire libre como en espacios cerrados.</p><p>Seleccionamos aderezos para el helado como frutas frescas, salsas de frutas y chocolate, chispas crujientes, nueces o mini galletas, permitiendo a los invitados crear sus propias composiciones de sabores.</p></div>
        <div class="modal-section"><h4>SELECCIÓN DE SABORES Y ADEREZOS</h4><p>Puedes confiarnos la selección de sabores y aderezos: adaptaremos la configuración al carácter del evento, la temporada y el perfil de los invitados. También es posible una total libertad para componer la lista tú mismo, para que la oferta se adapte perfectamente a tu concepto.</p></div>
        <div class="modal-section"><h4>¿DÓNDE FUNCIONA LA ESTACIÓN DE HELADOS?</h4><ul><li>Eventos corporativos y jornadas de puertas abiertas</li><li>Bodas, post-bodas y recepciones de verano</li><li>Galas, estrenos, eventos de imagen</li><li>Fiestas familiares, cumpleaños, comuniones</li><li>Eventos al aire libre y picnics</li></ul></div>
        <div class="modal-section"><h4>¿QUÉ GANAS?</h4><ul><li>Estación de helados estética y móvil adaptada al carácter del evento</li><li>Servicio integral – desde la preparación hasta el servicio durante el evento</li><li>Cremoso helado italiano servido en vivo con aderezos seleccionados profesionalmente</li><li>Un punto que atrae naturalmente a los invitados y construye asociaciones positivas con el evento.</li></ul></div>`,
            },
            cheese: {
                title: 'Tabla de Quesos',
                content: `<p class="modal-lead">Una fascinante zona de sabor que realza el valor de tu evento.</p>
        <div class="modal-section"><h4>¿CÓMO FUNCIONA?</h4><p>Nuestro puesto ofrece 12 artículos cuidadosamente seleccionados: quesos y embutidos italianos de alta calidad, complementados con exquisitos aderezos salados. Todo esto se combina en un conjunto armonioso, enriquecido con salsas especialmente seleccionadas que realzan el sabor de cada degustación. Los invitados crean sus propias mini composiciones de tablas de quesos, eligiendo libremente sus ingredientes favoritos: desde quesos cremosos con embutidos curados, pasando por aderezos crujientes, hasta salsas refinadas que realzan el carácter italiano del conjunto.</p></div>
        <div class="modal-section"><h4>SELECCIÓN CUIDADOSAMENTE COMPUESTA DE INGREDIENTES</h4><p>La selección de los 12 artículos y salsas está precisamente pensada: cada ingrediente ha sido seleccionado para complementar perfectamente a los demás, creando un conjunto armonioso de sabor y visual. La composición fue creada pensando en los más altos estándares de experiencias de degustación, garantizando a los invitados impresiones profesionales de clase mundial. Confiarnos este aspecto te permite concentrarte en la organización del evento, mientras nosotros aseguramos la consistencia y calidad de cada porción.</p></div>
        <div class="modal-section"><h4>¿DÓNDE FUNCIONA EL PUESTO DE QUESOS?</h4><ul><li>Eventos corporativos, banquetes y cócteles</li><li>Bodas, fiestas pre-boda y post-bodas</li><li>Galas, vernissages, lanzamientos de productos</li><li>Fiestas privadas íntimas y reuniones de negocios</li><li>Conferencias y reuniones de networking</li></ul></div>
        <div class="modal-section"><h4>¿QUÉ GANAS?</h4><ul><li>Puesto estético y móvil con quesos y embutidos italianos de primera calidad</li><li>Servicio integral: desde la disposición hasta el servicio y la reposición durante el evento</li><li>12 artículos seleccionados con precisión más salsas que realzan la degustación</li><li>Un punto que atrae naturalmente a los invitados y construye asociaciones positivas con el evento.</li></ul></div>`,
            }
        }
    },

  };

  let tick = false;
  let currentGalleryIndex = 0;
  // Current language state
  let currentLang = 'pl'; 

  const galleryImages = [];

  // --- Cache Elements (Optimization) ---
  const ui = {
    brand: null, brandText1: null, brandText2: null,
    nav: null, hamburger: null, bg: null, scroll: null,
    videoBg: null, onasSection: null, gallerySection: null
  };

  function cacheElements() {
    ui.brand = document.getElementById('brand');
    ui.brandText1 = document.getElementById('brandText1');
    ui.brandText2 = document.getElementById('brandText2');
    ui.nav = document.getElementById('nav');
    ui.hamburger = document.getElementById('hamburger');
    ui.bg = document.getElementById('bg');
    ui.scroll = document.getElementById('scroll');
    ui.videoBg = document.getElementById('videoBg');
    ui.onasSection = document.getElementById('onas');
    ui.gallerySection = document.getElementById('realizacje');
    ui.gallerySection = document.getElementById('realizacje');
    // NOTE: measureLayout() deferred to avoid blocking start-up
  }

  function measureLayout() {
      // ui.onasSection offset removed (unused)
      if (ui.gallerySection) {
          ui.cachedGalleryOffset = ui.gallerySection.offsetTop;
          ui.cachedGalleryHeight = ui.gallerySection.offsetHeight;
      }
  }

  // --- Initial Setup ---
  document.addEventListener('DOMContentLoaded', function() {
    cacheElements(); // Initialize cache
    updateLayout(); // Initial layout check (safe, no offsets used)
    
    // Defer expensive layout measurements to avoid forced reflow on load
    setTimeout(() => {
        measureLayout(); 
    }, 50);

    // Video fade in slightly later to prioritize LCP
    setTimeout(() => {
      if (ui.videoBg) ui.videoBg.classList.add('visible');
    }, 100);
  });

  // --- Scroll & Layout Logic ---
  function updateLayout(scrollY, vh) {
    // Optimization: Use cached elements
    
    // Safety check if elements exist (e.g. if script loads before DOM - though we use 'load' event)
    if (!ui.brand) cacheElements(); 
    
    // Use passed values or fallback (fallback for direct calls outside loop)
    scrollY = (scrollY !== undefined) ? scrollY : window.pageYOffset;
    vh = (vh !== undefined) ? vh : window.innerHeight;

    const progress = Math.min(scrollY / vh, 1);
    const textProgress = scrollY / vh; 
    
    // --- Navbar & Logo Visibility Logic ---
    let shouldShowNavbar = false;
    
    // User Request: Trigger when approaching "NIE TWORZYMY CATERINGU..."
    const onasHeadline = document.querySelector('#onas h2');
    if (onasHeadline) {
        const headlineRect = onasHeadline.getBoundingClientRect();
        // Trigger when the headline is getting close to the center/top of viewport
        // headlineRect.top is relative to viewport. 
        // When it appears from bottom: headlineRect.top < window.innerHeight
        // User wants "when approaching", so maybe when it's about to enter or entered?
        // "zbliża do napisu" - closer to the inscription.
        // Let's trigger when the headline is within the bottom 1/3 of the screen or higher.
        if (headlineRect.top < (window.innerHeight * 0.8)) {
            shouldShowNavbar = true;
        }
    } else {
        // Fallback
        if (scrollY >= (vh - 50)) { 
            shouldShowNavbar = true;
        }
    }




    // --- Hero Animation Sequence (Stretched) ---
    // User requested delay: Start at 30% scroll instead of 10%
    if (textProgress > 0.3) {
        // Step 1: Scroll started -> Move Logo (Scale Down), Hide Scroll Arrow
        if (ui.brand) {
            ui.brand.classList.add('moving'); 
            // Removed opacity=0 here so logo stays visible while moving
        }
        if (ui.scroll) ui.scroll.classList.add('hidden');
      
        // Step 2 & 3: Text Sequence
        if (textProgress < 1.0) {
            if (ui.brandText1) { ui.brandText1.classList.remove('hidden'); ui.brandText1.classList.add('visible'); }
            if (ui.brandText2) { ui.brandText2.classList.remove('visible'); ui.brandText2.classList.add('hidden'); }
        } else {
            if (ui.brandText1) { ui.brandText1.classList.remove('visible'); ui.brandText1.classList.add('hidden'); }
            if (ui.brandText2) { ui.brandText2.classList.remove('hidden'); ui.brandText2.classList.add('visible'); }
        }

    } else {
        // Reset: At top -> Show Logo (Center), Show Scroll Arrow
        if (ui.brand) {
            ui.brand.classList.remove('moving');
            ui.brand.style.opacity = '1';
        }
        if (ui.scroll) ui.scroll.classList.remove('hidden');
        if (ui.brandText1) { ui.brandText1.classList.remove('visible'); ui.brandText1.classList.add('hidden'); }
        if (ui.brandText2) { ui.brandText2.classList.remove('visible'); ui.brandText2.classList.add('hidden'); }
    }
    
    // Text fade out at bottom of Hero
    if (textProgress > 1.5) {
      if (ui.brandText1) { ui.brandText1.classList.remove('visible'); ui.brandText1.classList.add('hidden'); }
      if (ui.brandText2) { ui.brandText2.classList.remove('visible'); ui.brandText2.classList.add('hidden'); }
    }

    // --- Navbar State Apply ---
    if (shouldShowNavbar) {
        if (ui.bg) ui.bg.classList.add('shrink');
        if (ui.nav) ui.nav.classList.add('visible');
        if (ui.hamburger) ui.hamburger.classList.add('visible');
    } else {
        if (ui.bg) ui.bg.classList.remove('shrink');
        if (ui.nav) ui.nav.classList.remove('visible');
        if (ui.hamburger) ui.hamburger.classList.remove('visible');
    }
    
    tick = false;
  }
  
  // --- Actions ---
  function scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // --- Mobile Menu Logic (Simpler Overflow Lock) ---
  const hamburger = document.getElementById('hamburger');
  const nav = document.getElementById('nav');

  if (hamburger && nav) {
      hamburger.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          
          const isActive = hamburger.classList.contains('active');
          const html = document.documentElement;
          
          if (!isActive) {
              // Opening
              hamburger.classList.add('active');
              nav.classList.add('mobile-active');
              document.body.classList.add('nav-open');
              
              // Lock Scroll
              document.body.style.overflow = 'hidden';
              
          } else {
              // Closing
              hamburger.classList.remove('active');
              nav.classList.remove('mobile-active');
              document.body.classList.remove('nav-open');
              
              // Unlock Scroll
              document.body.style.overflow = '';
          }
      });

      // Close on link click
      nav.querySelectorAll('a').forEach(link => {
          link.addEventListener('click', () => {
              if (hamburger.classList.contains('active')) {
                  hamburger.classList.remove('active');
                  nav.classList.remove('mobile-active');
                  document.body.classList.remove('nav-open');
                  document.body.style.overflow = '';
              }
          });
      });
  }



  function updateGalleryParallax(scrollY, vh) {
    const gallerySection = ui.gallerySection;
    // Select only the columns that are explicitly marked as parallax
    const parallaxColumns = document.querySelectorAll('.gallery-column.parallax');
    
    if (gallerySection && parallaxColumns.length > 0) {
      // Robust calculation: Use direct viewport position
      // This works even if layout shifts occurred
      const rect = gallerySection.getBoundingClientRect();
      const windowHeight = vh || window.innerHeight;
      
      // Check if section is in viewport (with some buffer)
      if (rect.top < windowHeight && rect.bottom > 0) {
          
        // Calculate progress: 0 when top enters bottom, 1 when bottom leaves top
        // But for parallax we usually want 0 at center or -1 to 1 based on viewport traversal
        
        // Simple shift: Move UP or DOWN based on scroll
        // Center point: When section center is at viewport center
        const sectionCenter = rect.top + (rect.height / 2);
        const viewportCenter = windowHeight / 2;
        
        // dist is pixels from center
        const dist = sectionCenter - viewportCenter;
        
        // Factor: How much to move per pixel of scroll
        const factor = 0.15; 
        
        parallaxColumns.forEach((col, index) => {
          // Odd/Even logic: 
          // If 2 columns (indices 0, 1): 0 is down, 1 is up?
          // User wants "movement". Let's try simple opposite directions or just downward lag
          // Standard parallax: Move element slower than scroll (translateY > 0 when scrolling down?)
          
          // Let's make index 0 (Col 2) go DOWN, index 1 (Col 4) go UP relative to scroll?
          // Or both have localized shifts.
          
          const direction = (index % 2 === 0) ? -1 : 1;
          const movement = dist * factor * direction;
          
          col.style.transform = `translateY(${movement}px)`;
        });
      }
    }
  }

  // --- Optimized Resize Handler ---
  // Recalculate offsets ONLY on resize, not every scroll frame
  window.addEventListener('resize', () => {
    measureLayout();
    updateLayout();
  }, { passive: true });

  // Initial Calculation
  // measureLayout called in cacheElements

  // --- Scroll Handler (Optimized) ---
  // Use passive listener for better scroll performance
  window.addEventListener('scroll', () => {
    if (!tick) {
      requestAnimationFrame(() => {
        const scrollY = window.pageYOffset;
        const vh = window.innerHeight;
        updateLayout(scrollY, vh);
        updateGalleryParallax(scrollY, vh);
        tick = false;
      });
      tick = true;
    }
  }, { passive: true });
  
  // Initial check
  updateLayout();

  // --- Observer ---
  const sectionObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) entry.target.classList.add('visible');
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

  function toggleBodyScroll(lock) {
    if (lock) {
      // document.body.style.overflow = 'hidden'; // User requested to keep background scrollable
      document.body.classList.add('lightbox-open');
    } else {
      document.body.style.overflow = 'auto';
      document.body.classList.remove('lightbox-open');
    }
  }

  function openOfferModal(type) {
    const modal = document.getElementById('modal');
    const img = document.getElementById('modalImg');
    const title = document.getElementById('modalTitle');
    const text = document.getElementById('modalText');
    
    // Get data from translations based on current language
    const data = translations[currentLang]?.modals?.[type];
    
    if (data && modal) {
      let imgSrc = data.image; // May be undefined now

      // 1. Try Specific Modal Image from Config
      if (window.siteContentConfig && 
          window.siteContentConfig.offer_modals && 
          window.siteContentConfig.offer_modals[type]) {
          imgSrc = window.siteContentConfig.offer_modals[type] + '?v=' + Date.now();
      } 
      // 2. Fallback to Card Image from Config if Modal Image not set
      else if (window.siteContentConfig && 
               window.siteContentConfig.offer_cards && 
               window.siteContentConfig.offer_cards[type]) {
          imgSrc = window.siteContentConfig.offer_cards[type] + '?v=' + Date.now();
      }
      
      // 3. Last resort fallback (absolute path if needed, or placeholder)
      if (!imgSrc) {
          // Optional: Set a default placeholder if nothing exists
          // imgSrc = 'assets/images/placeholder.jpg';
      }
      
      img.src = imgSrc;
      img.alt = data.title;
      title.textContent = data.title;
      text.innerHTML = data.content; // Render HTML directly
      modal.classList.add('active');
      toggleBodyScroll(true);
      
      // History API: Add state
      history.pushState({ modal: 'offer' }, '', '#offer-' + type);
    }
  }

  function openGalleryModal(index) {
    currentGalleryIndex = index;
    const modal = document.getElementById('galleryModal');
    const img = document.getElementById('galleryModalImg');
    
    if (modal && img) {
      img.src = galleryImages[index];
      // Reset styles in case they were stuck in transition
      img.style.opacity = '1';
      img.style.transform = 'scale(1)';
      
      modal.classList.add('active');
      toggleBodyScroll(true);
      
      // History API: Add state
      history.pushState({ modal: 'gallery' }, '');
    }
  }

  function closeAllModals() {
    document.querySelectorAll('.modal, .gallery-modal').forEach(m => m.classList.remove('active'));
    toggleBodyScroll(false);
    
    // Clean URL hash if present
    if (window.location.hash.startsWith('#offer-')) {
        history.replaceState(null, '', window.location.pathname + window.location.search);
    }
  }

  function changeGalleryImage(direction) {
    currentGalleryIndex += direction;
    if (currentGalleryIndex < 0) currentGalleryIndex = galleryImages.length - 1;
    if (currentGalleryIndex >= galleryImages.length) currentGalleryIndex = 0;
    
    const img = document.getElementById('galleryModalImg');
    if (img) {
      img.style.opacity = '0';
      img.style.transform = 'scale(0.8)';
      setTimeout(() => {
        img.src = galleryImages[currentGalleryIndex];
        img.style.opacity = '1';
        img.style.transform = 'scale(1)';
      }, 200);
    }
  }

  // --- Initialization ---
  document.addEventListener('DOMContentLoaded', function() {
    
    // Observers
    document.querySelectorAll('.section, .section-premium').forEach(s => sectionObserver.observe(s));

    // Offer Cards
    document.querySelectorAll('.offer-card').forEach(card => {
      card.addEventListener('click', () => openOfferModal(card.getAttribute('data-offer')));
    });

    // Gallery Items
    document.querySelectorAll('.gallery-item').forEach(item => {
      const img = item.querySelector('img');
      const index = parseInt(item.getAttribute('data-index'));
      galleryImages[index] = img.src;
      item.addEventListener('click', () => openGalleryModal(index));
    });
    
    // --- Dynamic Content Loaders ---
    
    // Gallery Animation Observer (Defined here to be accessible)
    const galleryObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                galleryObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    // Initial Observation for Static Items
    document.querySelectorAll('.gallery-item').forEach(item => {
        galleryObserver.observe(item);
    });
    
    
    // Defer non-critical API calls to clear the Critical Request Chain
    const loadDynamicContent = () => {
        // 1. Availability Notice
        fetch('api/get_status.php?v=' + Date.now())
        .then(r => r.json())
    .then(data => {
        const notice = document.getElementById('availability-notice');
        if (data && data.enabled && data.text && notice) {
            // Elegant SVG Icon (restored)
            notice.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" class="notice-icon" style="stroke: var(--color-accent); min-width: 20px;">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="1.5"></rect>
                    <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="1.5"></line>
                    <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="1.5"></line>
                    <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="1.5"></line>
                </svg>
                <span>${data.text}</span>
            `;
            notice.style.display = 'flex';
        }
    })
    .catch(() => { /* Silent fail locally */ });

    // 1.5 Dynamic Site Content (Hero, Offer, About)
    fetch('assets/data/content.json?v=' + Date.now())
    .then(r => r.json())
    .then(content => {
        if(!content) return;

        // Hero Video
        if(content.hero_video) {
            const vid = document.querySelector('#videoBg video source');
            if(vid) {
                // Only update if changed prevents flickering usually, but simple src swap is fine
                if(!vid.src.includes(content.hero_video)) {
                     vid.src = content.hero_video;
                     vid.parentElement.load();
                }
            }
        }

        // About Image
        if(content.about_image) {
            const img = document.querySelector('#onas .premium-image');
            if(img) img.src = content.about_image + '?v=' + Date.now();
        }

        // Offer Main Image
        if(content.offer_main_image) {
            const img = document.querySelector('#oferta .premium-image');
            if(img) img.src = content.offer_main_image + '?v=' + Date.now();
        }

        // Offer Cards
        if(content.offer_cards) {
            const setCardBg = (type, url) => {
                const card = document.querySelector(`.offer-card[data-offer="${type}"] .offer-image`);
                if(card && url) {
                    card.style.backgroundImage = `url('${url}')`;
                    
                    // Also update the modal trigger data if needed, or we just handle it in openOfferModal
                    // Actually, we should store this global config to be used by openOfferModal
                    window.siteContentConfig = content;
                }
            };
            setCardBg('pancakes', content.offer_cards.pancakes);
            setCardBg('icecream', content.offer_cards.icecream);
            setCardBg('cheese', content.offer_cards.cheese);
        }

        // Gallery Parallax Background
        if(content.gallery_bg) {
            // Assuming #bg or a new element. User said "background behind gallery".
            // Since there isn't a dedicated bg element for gallery in HTML, we might target .background-overlay 
            // or create a specific CSS rule. Let's assume user refers to the global parallax #bg or similar.
            // But looking at HTML, #videoBg is fixed. #bg is fixed overlay.
            // Let's look for .parallax class columns inside #realizacje.
            // The user likely means the background visible behind the parallax columns.
            // Actually, usually in this design style, the body/fixed bg acts as the parallax layer.
            // Let's set the #bg background image if it's meant to be global, OR
            // set it for the specific section if defined. 
            // Given the request "background behind gallery", let's try setting it to #bg 
            // (but this overrides global video overlay).
            // ALTERNATIVE: Target `.gallery-column.parallax` or the section itself.
            // Safe bet: Set it as style on #bg to replace default overlay if desired
            // OR if user meant the parallax effect specifically.
            const bg = document.getElementById('bg');
            if(bg) {
                // Ensure bg image also refreshes if changed
                bg.style.backgroundImage = `url('${content.gallery_bg}?v=${new Date().getTime()}')`;
                bg.style.backgroundSize = 'cover';
                bg.style.backgroundPosition = 'center';
            }
        }

        // Why Us Background
        if(content.why_us_bg) {
            const whySection = document.querySelector('.parallax-why');
            if(whySection) {
                whySection.style.backgroundImage = `url('${content.why_us_bg}?v=${new Date().getTime()}')`;
            }
        }
    })
    .catch(() => {})
    .finally(() => {
        // Critical: Re-measure layout/offsets after images likely affect DOM flow or simply after data load
        setTimeout(measureLayout, 500); 
    });

    const galleryGrid = document.getElementById('dynamicGalleryGrid');
    if (galleryGrid) {
        fetch('api/get_gallery.php?v=' + Date.now())
        .then(r => r.json())
        .then(images => {
            if (images.length === 0) return; 

            // SAFETY: Logic removed to prevent duplication of images in small galleries.
            // We now rely on dynamic column count in PHP/CSS or just render what we have.
            // const MIN_IMAGES = 15; // Removed

            // Clear static items only if we have API images
            const columns = galleryGrid.querySelectorAll('.gallery-column');
            if (columns.length === 0) return;
            
            // Clear existing static content for replacement
            columns.forEach(col => col.innerHTML = ''); 

            // Reset gallery array for dynamic items
            // Note: We'll overwrite existing static entries in the array 
            // as we rebuild indices starting from 0.
            
            let globalIndex = 0;
            
            images.forEach((src, i) => {
                const colIndex = i % columns.length;
                const col = columns[colIndex];
                
                const div = document.createElement('div');
                div.className = 'gallery-item';
                div.setAttribute('data-index', globalIndex);
                
                const img = document.createElement('img');
                // The API returns 'assets/gallery/filename.jpg' or full URL. 
                // Since this script runs on index.html, relative paths are correct.
                img.src = src; 
                img.loading = 'lazy';
                img.alt = 'Realizacja ' + (globalIndex + 1);
                
                // Styling fix to ensure visibility
                img.style.width = '100%';
                img.style.display = 'block';
                
                div.appendChild(img);
                col.appendChild(div);
                
                // Add to lightbox array
                galleryImages[globalIndex] = src;
                
                // Add Click Listener
                const idx = globalIndex;
                div.addEventListener('click', () => openGalleryModal(idx));
                
                // Add to observer
                galleryObserver.observe(div);
                
                globalIndex++;
            });
        })
        .catch(err => { 
             // Silent fail locally - Static items remain valid
             // console.log('Running in local/offline mode regarding gallery API');
        });
    }
} // End loadDynamicContent

    // Execute deferred loading
    if ("requestIdleCallback" in window) {
        requestIdleCallback(loadDynamicContent, { timeout: 2000 });
    } else {
        setTimeout(loadDynamicContent, 100);
    }

    // --- Global Click/Key Handlers
    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('modal') || 
          e.target.classList.contains('gallery-modal') ||
          e.target.classList.contains('modal-close') ||
          e.target.classList.contains('gallery-modal-close')) {
         
         // If closing manually, go back in history to resolve the state
         if (history.state && (history.state.modal === 'offer' || history.state.modal === 'gallery')) {
             history.back();
         } else {
             closeAllModals();
         }
      }
    });

    // History API Handler (Back Button)
    window.addEventListener('popstate', (e) => {
        // If state is null or doesn't have our modal flag, close modals
        if (!e.state || (!e.state.modal)) {
            closeAllModals();
        }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
          if (history.state && (history.state.modal === 'offer' || history.state.modal === 'gallery')) {
             history.back();
          } else {
             closeAllModals();
          }
      }
      if (document.getElementById('galleryModal').classList.contains('active')) {
        if (e.key === 'ArrowLeft') changeGalleryImage(-1);
        if (e.key === 'ArrowRight') changeGalleryImage(1);
      }
    });

    // Gallery Buttons
    const prevBtn = document.querySelector('.gallery-prev');
    const nextBtn = document.querySelector('.gallery-next');
    if (prevBtn) prevBtn.addEventListener('click', (e) => { e.stopPropagation(); changeGalleryImage(-1); });
    if (nextBtn) nextBtn.addEventListener('click', (e) => { e.stopPropagation(); changeGalleryImage(1); });

    // Contact Form
    const form = document.getElementById('form');
    
    // Trigger "Poor Man's Cron" cleanly after page load (Zero SEO/Performance impact)
    window.addEventListener('load', () => {
        if ("requestIdleCallback" in window) {
            requestIdleCallback(() => sendPing());
        } else {
            setTimeout(() => sendPing(), 2000);
        }
    });

    function sendPing() {
        fetch('api/contact.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'ping' })
        }).catch(() => {}); 
    }

    if (form) {
      const progressBar = document.getElementById('form-progress');
      const progressText = document.getElementById('progress-text');
      const fields = form.querySelectorAll('input, select, textarea');
      
      // Progress Bar Logic
      function updateProgress() {
        if (!progressBar || !progressText) return;
        
        let filled = 0;
        let total = 0;
        
        // Count significant fields (ONLY REQUIRED)
        fields.forEach(f => {
            if (f.type !== 'hidden' && f.type !== 'submit' && f.type !== 'checkbox') {
                // Only count if required
                if (f.hasAttribute('required')) {
                    total++;
                    if (f.value.trim() !== '') filled++;
                }
            }
        });
        
        // Checkboxes count as 1 group (Manual check as they are required by logic)
        const snowflakes = form.querySelectorAll('input[name="stations"]');
        if (snowflakes.length > 0) {
            total++;
            if (Array.from(snowflakes).some(cb => cb.checked)) filled++;
        }

        const percent = Math.round((filled / total) * 100);
        progressBar.style.width = percent + '%';
        progressText.textContent = `Uzupełnij dane, abyśmy mogli przygotować ofertę (${percent}%)`;
      }
      
      fields.forEach(f => {
          f.addEventListener('input', updateProgress);
          f.addEventListener('change', updateProgress);
          
          // Silent Autosave Logic
          f.addEventListener('blur', () => {
             const formDataRaw = new FormData(form);
             const data = Object.fromEntries(formDataRaw.entries());
             
             // Map checkboxes manually if needed, but for autosave basic info is key
             // Only save if we have at least phone or email
             if (data.phone || data.email) {
                 
                 // Collect stations for draft
                 const stations = Array.from(form.querySelectorAll('input[name="stations"]:checked'))
                    .map(cb => cb.nextElementSibling.textContent).join(', ');
                 
                 const payload = {
                     ...data,
                     stations: stations, // Ensure stations are sent in draft too
                     is_partial: true
                 };

                 fetch('api/contact.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                 }).catch(() => {}); // Silent fail is fine for autosave
             }
          });
      });

      // Phone Validation (Input Masking)
      const phoneInput = form.querySelector('input[name="phone"]');
      if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            // Allow only digits, spaces, and '+' at start
            let val = e.target.value;
            val = val.replace(/[^\d\s+]/g, ''); // Remove illegal chars
            
            // max length 15 (safe limit for nearly all international numbers)
            if (val.length > 20) val = val.substring(0, 20); 
            
            e.target.value = val;
        });
      }

      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // --- Visual Validation ---
        let isValid = true;
        
        // Clear previous errors
        form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        form.querySelectorAll('.error-message').forEach(el => el.remove());

        // Validate text/select fields
        fields.forEach(f => {
            if (f.hasAttribute('required') && !f.value.trim()) {
                isValid = false;
                f.classList.add('input-error');
                
                const msg = document.createElement('small');
                msg.className = 'error-message';
                // Use translation key: form.required
                msg.textContent = translations[currentLang]?.form?.required || "Required";
                f.parentNode.appendChild(msg);
            }
        });

        // Validate Stations
        const checkboxes = form.querySelectorAll('input[name="stations"]');
        const stationsChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (!stationsChecked) {
             isValid = false;
             // Find container to highlight
             const container = form.querySelector('.checkbox-group');
             if (container) {
                 const msg = document.createElement('small');
                 msg.className = 'error-message';
                 msg.style.marginTop = '10px';
                 // Use translation key: form.stations_error
                 msg.textContent = translations[currentLang]?.form?.stations_error || "Select a station";
                 container.parentNode.appendChild(msg);
             }
        }

        if (!isValid) {
            // Scroll to first error
            const firstError = form.querySelector('.input-error, .error-message');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return; // Stop submission
        }
        
        // --- Submission ---
        const stations = Array.from(form.querySelectorAll('input[name="stations"]:checked'))
          .map(cb => cb.nextElementSibling.textContent).join(', '); // Use label text directly
        
        const formData = {
          website_check: form.website_check.value, // Antispam
          name: form.name.value,
          email: form.email.value,
          phone: form.phone.value,
          date: form.date.value,
          guests: form.guests.options[form.guests.selectedIndex].text,
          budget: form.budget.options[form.budget.selectedIndex].text,
          event_type: form.event_type.options[form.event_type.selectedIndex].text,
          stations: stations,
          message: form.message.value || 'Brak dodatkowej wiadomości'
        };
        
        const submitBtn = form.querySelector('.cta-primary');
        const originalText = submitBtn.textContent;
        // Use translation for "Sending..."
        submitBtn.textContent = translations[currentLang]?.form?.sending || 'Sending...';
        submitBtn.disabled = true;
        
        // Send to PHP script
        fetch('api/contact.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const successMsg = translations[currentLang]?.form?.success_msg || 'Success!';
                alert(successMsg);
                form.reset();
                updateProgress(); // Reset progress bar
            } else {
                alert('❌ ' + (data.message || 'Error'));
            }
        })
        .catch((err) => {
            console.error('Error:', err);
            const errorMsg = translations[currentLang]?.form?.error_msg || 'Error sending.';
            alert('❌ ' + errorMsg);
        })
        .finally(() => {
            submitBtn.textContent = translations[currentLang]?.form?.submit || originalText; 
            submitBtn.disabled = false;
        });
      });
    }

    // Brand Scroll To Top
    const brand = document.getElementById('brand');
    if (brand) {
      brand.addEventListener('click', scrollToTop);
    }

    updateLayout();

    // Language Logic
    const langBtns = document.querySelectorAll('.lang-btn');
    
    function updateLanguage(lang) {
        currentLang = lang; // Update global state
        langBtns.forEach(btn => btn.classList.toggle('active', btn.dataset.lang === lang));
        
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            const keys = key.split('.');
            let text = translations[lang];
            
            if (text) {
                keys.forEach(k => { 
                    if (text) text = text[k]; 
                });
            }

            if (typeof text === 'string') {
                el.innerHTML = text;
            }
        });

        // Handle Placeholders
        document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
            const key = el.getAttribute('data-i18n-placeholder');
            const keys = key.split('.');
            let text = translations[lang];
            
            if (text) {
                keys.forEach(k => { 
                    if (text) text = text[k]; 
                });
            }

            if (typeof text === 'string') {
                el.placeholder = text;
            }
        });
    }

    langBtns.forEach(btn => {
        btn.addEventListener('click', () => updateLanguage(btn.dataset.lang));
    });
  });

})();

// --- Cookie & Analytics Logic ---
document.addEventListener('DOMContentLoaded', () => {
    
    function loadAnalytics() {
        // Prevent double loading
        if (document.getElementById('ga-script')) return;
        
        const script = document.createElement('script');
        script.id = 'ga-script';
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtag/js?id=G-K4ZBMDXLW7';
        document.head.appendChild(script);

        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-K4ZBMDXLW7');
    }

    const consent = localStorage.getItem('raricart_cookies_consent');

    if (consent === 'granted') {
        loadAnalytics();
    } else if (consent === 'denied') {
        // Do nothing (Analytics blocked)
    } else {
        // Migration check: If old key exists, treat as granted
        if (localStorage.getItem('raricart_cookies_accepted')) {
             localStorage.setItem('raricart_cookies_consent', 'granted');
             localStorage.removeItem('raricart_cookies_accepted'); // Cleanup
             loadAnalytics();
        } else {
            // Show banner (No choice made yet)
            const banner = document.getElementById('cookie-banner');
            if (banner) {
                banner.style.display = 'block';
                setTimeout(() => banner.style.opacity = '1', 10);
            }
        }
    }

    // Accept Button
    const acceptBtn = document.getElementById('accept-cookies');
    if (acceptBtn) {
        acceptBtn.addEventListener('click', () => {
             localStorage.setItem('raricart_cookies_consent', 'granted');
             loadAnalytics();
             closeBanner();
        });
    }

    // Reject Button
    const rejectBtn = document.getElementById('reject-cookies');
    if (rejectBtn) {
        rejectBtn.addEventListener('click', () => {
             localStorage.setItem('raricart_cookies_consent', 'denied');
             closeBanner();
        });
    }

    function closeBanner() {
        const banner = document.getElementById('cookie-banner');
        if (banner) {
            banner.style.opacity = '0';
            setTimeout(() => banner.style.display = 'none', 500);
        }
    }
    // Cookie & Analytics logic remains here
});
