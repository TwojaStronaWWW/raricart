;(function () {
	'use strict'

	// Translations
	const translations = {
		pl: {
			nav: {
				about: 'O Nas',
				offer: 'Oferta',
				gallery: 'Galeria',
				faq: 'FAQ',
				why_us: 'Co Nas Wyróżnia',
				contact: 'Kontakt',
				check_date: 'Sprawdź termin',
				packages: 'Pakiety',
			},
			hero: {
				title1: 'TAM, GDZIE SMAK SPOTYKA EMOCJE, A PROSTOTA STAJE SIĘ ELEGANCJĄ...',
				title2: '...TAM ZACZYNA SIĘ <span class="logo-pulse">RARICART</span>',
				scroll: 'Przewiń w dół',
			},
			trust_bar: {
				badge: 'Doświadczenie kulinarne',
				headline: 'Jedzenie, które dzieje się na oczach gości.',
				pillar1_title: 'ŚWIEŻO PRZYGOTOWYWANE',
				pillar1_desc: 'Każda porcja powstaje na żywo na oczach Twoich gości.',
				pillar2_title: 'WŁASNE KOMPOZYCJE',
				pillar2_desc: 'Goście sami decydują o ulubionych dodatkach i smakach.',
				pillar3_title: 'PEŁNA OBSŁUGA',
				pillar3_desc: 'Od montażu, przez serwis, po demontaż i nienaganny porządek.',
				pillar4_title: 'MOBILNE STACJE',
				pillar4_desc: 'Działamy w każdej przestrzeni: plener, elegancka sala, biuro.',
			},
			stations: {
				badge: 'Co właściwie oferujemy?',
				title: 'Wybierz swoją stację',
				intro: 'Nie jesteśmy klasycznym bufetem. Nasze stacje przygotowują jedzenie na miejscu, na oczach gości. Każdy może wybrać swoje dodatki i stworzyć własną kompozycję.',
				see_all: 'ZOBACZ CAŁĄ OFERTĘ & PAKIETY',
				explore_btn: 'POZNAJ STACJĘ',
				badge_sweet: 'Na słodko',
				badge_refresh: 'Orzeźwienie',
				badge_savory: 'Wytrawnie',
				pancakes_desc: 'Ciepłe, puszyste mini pancakes z dodatkami i sosami przygotowywane na oczach gości.',
				icecream_desc: 'Kremowe lody włoskie, świeże owoce, rzemieślnicze sosy i chrupiące dodatki.',
				cheese_desc: 'Starannie dobrane sery, wędliny i wytrawne dodatki w eleganckiej oprawie.',
				combine_note: '<strong>Chcesz więcej niż jedną atrakcję?</strong> Możesz dowolnie łączyć stacje na jednym evencie — przygotujemy dla Ciebie indywidualny pakiet.',
				cta_check: 'SPRAWDŹ DOSTĘPNOŚĆ STACJI',
			},
			why_station: {
				stat_label: 'Na oczach gości',
				badge: 'Dlaczego stacja zamiast cateringu?',
				title: 'Nie tylko jedzenie. Atrakcja dla Twoich gości.',
				intro: 'Tradycyjny catering często stoi w podgrzewaczach i czeka na gości. My tworzymy kulinarne show, które angażuje zmysły i staje się naturalnym centrum rozmów na Twoim przyjęciu.',
				pillar1_title: 'SMAK',
				pillar1_desc: 'Świeże produkty przygotowywane i serwowane na bieżąco na oczach gości — bez kompromisów i bez odgrzewania.',
				pillar2_title: 'DOŚWIADCZENIE',
				pillar2_desc: 'Goście z zachwytem obserwują proces przygotowania, rozmawiają z obsługą i sami komponują swój wymarzony deser.',
				pillar3_title: 'ESTETYKA',
				pillar3_desc: 'Dopracowana, elegancka stacja mobilna staje się spójną, fotogeniczną częścią aranżacji Twojej sali lub pleneru.',
				benefit_box_label: 'Bezstresowa organizacja',
				benefit_box_p1: 'A Ty? Nie musisz organizować obsługi, przygotowywać stanowiska ani martwić się o sprzątanie.',
				benefit_box_rhythm: 'Przyjeżdżamy. Przygotowujemy. Serwujemy. Sprzątamy.',
				benefit_box_p2: 'Ty zajmujesz się swoimi gośćmi. My zajmujemy się całą stacją.',
				cta_btn: 'CHCĘ TAKĄ STACJĘ NA SWOIM WYDARZENIU',
			},
			process: {
				badge: 'PROSTY PROCES WSPÓŁPRACY',
				title: 'Od pierwszej wiadomości do ostatniej porcji.',
				subtitle: 'Zero skomplikowanych formalności i zero stresu. Sprawdź, jak krok po kroku wygląda realizacja stacji Raricart na Twoim weselu, evencie firmowym lub przyjęciu.',
				step1_tag: 'Kontakt',
				step1_title: 'Opowiadasz nam o wydarzeniu',
				step1_desc: 'Podajesz datę, miejscowość, szacowaną liczbę gości oraz charakter imprezy. Wystarczy krótka wiadomość przez formularz.',
				step2_tag: 'Dopasowanie',
				step2_title: 'Dobieramy idealną stację',
				step2_desc: 'Podpowiadamy, które rozwiązanie (lub zestaw stacji) najlepiej sprawdzi się przy Twoim formacie przyjęcia i liczbie gości.',
				step3_tag: 'Logistyka 100%',
				step3_title: 'Przyjeżdżamy i szykujemy wszystko',
				step3_desc: 'Transport, montaż, sprzęt, świeże składniki i nienaganna estetyka stoiska — pełne przygotowanie jest całkowicie po naszej stronie.',
				step4_tag: 'Live Cooking',
				step4_title: 'Goście korzystają ze stacji',
				step4_desc: 'Wybierają ulubione dodatki, komponują własne smaki i wracają po kolejne porcje. Stacja tętni życiem, uśmiechem i aromatem.',
				step5_tag: 'Czystość',
				step5_title: 'My sprzątamy i demontujemy',
				step5_desc: 'Po zakończeniu serwisu sprawnie demontujemy stację i zostawiamy salę lub plener w idealnym porządku. Ty nie martwisz się o nic.',
				summary_highlight: 'Prościej się nie da.',
				summary_lead: 'Ty cieszysz się gośćmi i wyjątkowymi chwilami — my dbamy o kulinarny zachwyt i perfekcyjny serwis od A do Z.',
				microcopy: '⏱️ Odpowiadamy na zapytania zazwyczaj w ciągu kilku godzin.',
				cta_btn: 'ZAPYTAJ O SWÓJ TERMIN',
			},
			audiences: {
				badge: 'DLA KOGO JEST RARICART?',
				title: 'Gdzie pojawia się Raricart?',
				subtitle: 'Dopasowujemy stację do charakteru i harmonogramu wydarzenia. Zobacz, w jakich formatach sprawdzamy się najlepiej i wybierz scenariusz dla siebie.',
				card1_tag: 'Wesela & Poprawiny',
				card1_title: 'Wesela z charakterem',
				card1_desc: 'Słodka atrakcja na żywo, orzeźwiający deser po obiedzie, nocna przekąska po tańcach lub stylowa strefa chillout na sali weselnej i w plenerze.',
				card1_f1: 'Nowoczesna alternatywa dla candy baru',
				card1_f2: 'Strefa, przy której goście integrują się i robią zdjęcia',
				card1_f3: 'Płynny serwis bez kolejek i limitów porcji',
				card2_tag: 'Biznes & Korporacje',
				card2_title: 'Eventy firmowe i gale',
				card2_desc: 'Spotkania integracyjne, pikniki firmowe, konferencje, jubileusze, gale roczne i premiery produktów.',
				card2_f1: 'Wysoka przepustowość (obsługa od 50 do ponad 500 osób)',
				card2_f2: 'Pełna faktura VAT, ubezpieczenie i certyfikaty sanitarne',
				card2_f3: 'Możliwość personalizacji menu i brandingu',
				card3_tag: 'Uroczystości prywatne',
				card3_title: 'Urodziny i przyjęcia w ogrodzie',
				card3_desc: 'Okrągłe urodziny, rocznice, baby shower, chrzciny, komunie oraz swobodne przyjęcia w przydomowym ogrodzie lub wynajętym lokalu.',
				card3_f1: 'Kompaktowy rozmiar — mieścimy się na tarasie i w salonie',
				card3_f2: 'Radość dla dzieci i zachwyt dorosłych smakoszy',
				card3_f3: 'Ty bawisz się z rodziną, my zajmujemy się gastronomią',
				card4_tag: 'Agencje & B2B',
				card4_title: 'Agencje i Wedding Plannerzy',
				card4_desc: 'Niezawodny, estetyczny podwykonawca gastronomiczny jako gotowy, bezproblemowy moduł w scenariuszu Twojego wydarzenia.',
				card4_f1: 'Żelazna punktualność i bezgłośny, sprawny montaż',
				card4_f2: 'Nienaganny dress code i wysoka kultura osobista zespołu',
				card4_f3: 'Spokój koordynatora — bierzemy 100% odpowiedzialności za strefę',
				footer_title: 'Organizujesz coś innego?',
				footer_desc: 'Napisz do nas. Chętnie dopasujemy stację, menu i logistykę do każdego nietypowego formatu imprezy.',
				cta_btn: 'SKONSULTUJ SWÓJ EVENT',
			},
			why_raricart: {
				badge: '6 MOCNYCH PRZEWAG',
				title: 'Dlaczego właśnie Raricart?',
				subtitle: 'Nie jesteśmy klasycznym cateringiem w bemarach. Zobacz 6 kluczowych powodów, dla których goście tak bardzo zapamiętują nasze stacje live food.',
				card1_title: 'Świeżość na żywo',
				card1_desc: 'Każda porcja powstaje na bieżąco, na oczach Twoich gości. Ciepłe pancakes prosto z płyty, świeżo kręcone lody i pachnące dodatki — bez odgrzewania.',
				card2_title: 'Swoboda wyboru',
				card2_desc: 'Goście sami decydują o swojej kompozycji: autorskie sosy, świeże owoce, chrupiące posypki i unikalne smaki. Każdy tworzy dokładnie to, na co ma ochotę.',
				card3_title: 'Efekt WOW i integracja',
				card3_desc: 'Live cooking przyciąga wzrok, zachwyca zapachem i naturalnie skupia wokół siebie gości. To atrakcja, przy której rodzą się uśmiechy i pamiątkowe zdjęcia.',
				card4_title: 'Estetyka w każdym detalu',
				card4_desc: 'Nasze stoiska to eleganckie meble eventowe wykonane z dbałością o detal. Stają się spójną, fotogeniczną częścią aranżacji Twojej sali lub pleneru.',
				card5_title: 'Wygoda organizatora',
				card5_desc: 'Dojazd, montaż, sprzęt, obsługa, serwis i sprzątanie po evencie — w 100% po naszej stronie. Ty skupiasz się na gościach i spokojnie cieszysz się imprezą.',
				card6_title: 'Pełna elastyczność',
				card6_desc: 'Jedna stacja czy pakiet kilku smaków? Dopasowujemy ofertę, godziny serwisu i menu do charakteru przyjęcia — od 20 do ponad 500 gości.',
				cta_btn: 'SPRAWDŹ DOSTĘPNOŚĆ NA SWOJE WYDARZENIE',
			},
			about: {
				title: 'Nie tworzymy cateringu, lecz doświadczenie.',
				p1: 'Tworzymy mobilne stacje degustacyjne, które stają się ozdobą każdego wydarzenia. To nie tylko jedzenie, to <span class="highlight">subtelny, dopracowany i&nbsp;pełen charakteru</span> element scenografii.',
				p2: 'Serwujemy lekkie, świeże kompozycje — od puszystych mini pancakes, przez autentyczne włoskie lody, aż po aromatyczne deski serów. Budujemy atmosferę klasy i&nbsp;swobody, w&nbsp;której Twoi goście poczują się wyjątkowo.',
				stats: {
					s1: { num: '100%', text: 'Pasji i świeżości – każde danie przygotowujemy na oczach gości.' },
					s2: {
						num: '3',
						text: 'Unikalne stacje – Mini Pancakes, Lody Włoskie, Deski Serów (wyselekcjonowane i dopracowane).',
					},
					s3: { num: '0', text: 'Kompromisów – używamy tylko składników premium i naturalnych produktów.' },
					s4: { num: '∞', text: 'Możliwości – personalizujemy menu i wygląd stacji pod Twój event.' },
				},
			},
			offer: {
				title: 'Nasza Oferta',
				intro_title: 'NIE GOTUJEMY DAŃ, TWORZYMY CHWILE, KTÓRE ŁĄCZĄ LUDZI',
				p1: 'Nasze stacje stają się miejscem rozmów, uśmiechów i&nbsp;zdjęć, a&nbsp;my dbamy o&nbsp;każdy szczegół, od montażu po ostatni serwis, abyś mógł cieszyć się wydarzeniem tak samo jak Twoi goście.',
				p2: 'Obsługujemy eventy firmowe, wesela, gale<br> i&nbsp;prywatne przyjęcia, współpracując zarówno z&nbsp;agencjami, jak i&nbsp;klientami indywidualnymi.',
				p3: 'W&nbsp;każdym projekcie kierujemy się zasadą, że smak i&nbsp;estetyka mają tę samą wartość, razem tworzą atmosferę, której nikt nie zapomina. Z&nbsp;Raricart zyskujesz nie tylko catering, ale spójny, piękny element scenografii Twojego wydarzenia, który smakuje tak dobrze, jak wygląda.',
				cards: {
					pancakes: {
						title: 'Mini Pancakes',
						desc: 'Słodka stacja, która angażuje gości i&nbsp;staje się sercem wydarzenia.',
					},
					icecream: {
						title: 'Lody Włoskie',
						desc: 'Orzeźwiająca stacja, która zachwyca gości i&nbsp;buduje atmosferę.',
					},
					cheese: { title: 'Deska Serów', desc: 'Fascynująca strefa smaku z&nbsp;włoskimi serami i&nbsp;wędlinami.' },
				},
			},
			why: {
				title: 'SZTUKA KULINARNYCH DOŚWIADCZEŃ',
				cards: {
					1: {
						title: 'EFEKT „WOW" Z KLASĄ',
						desc: 'Na oczach gości serwujemy ciepłe pancakes prosto z&nbsp;patelni, nalewamy cremowe lody i&nbsp;komponujemy deski serów - wszystko świeże, personalizowane i&nbsp;dopracowane w&nbsp;detalach. Proces live station przyciąga uwagę, integruje uczestników i&nbsp;tworzy naturalne punkty spotkań, gdzie przy apetycznym widoku rodzą się rozmowy. Elegancki, mobilny design stacji podnosi prestiż wydarzenia, łącząc estetykę premium z&nbsp;czystą przyjemnością dla zmysłów.',
					},
					2: {
						title: 'MNIEJ LOGISTYKI, WIĘCEJ SPOKOJU',
						desc: 'Raricart przejmuje całość: dojazd, montaż stacji, serwowanie podczas eventu, demontaż i&nbsp;perfekcyjny porządek po zakończeniu. Nie wymagamy zaplecza kuchennego - mobilne stacje działają wszędzie: w&nbsp;loftach, ogrodach, halach czy nietypowych przestrzeniach eventowych. Zespół synchronizuje serwis z&nbsp;harmonogramem, dba o&nbsp;płynny przepływ gości i&nbsp;minimalizuje kolejki.',
					},
					3: {
						title: 'DOŚWIADCZENIE ZAMIAST BUFETU',
						desc: 'W&nbsp;odróżnieniu od statycznego bufetu, nasze live stations angażują: goście obserwują nalewanie lodów, układanie pancakes i&nbsp;komponowanie desek serów, wybierając dodatki na bieżąco. Wszystko serwowane porcjami „tu i&nbsp;teraz" - świeże, bez marnowania, idealnie dopasowane do liczby i&nbsp;preferencji uczestników. Tematyczne stacje stają się magnesem na gości, budując emocje i&nbsp;niezapomniane wspomnienia.',
					},
					4: {
						title: 'BEZPIECZEŃSTWO, JAKOŚĆ, ESTETYKA',
						desc: 'Przestrzegamy rygorystycznych standardów higieny i&nbsp;bezpieczeństwa żywności, z&nbsp;naciskiem na świeżość składników i&nbsp;perfekcyjną prezencję. Używamy wyselekcjonowanych produktów serwowanych w&nbsp;optymalnej temperaturze. Każdy detal - od aranżacji stacji, przez zastawę, po pracę zespołu - tworzy spójną scenografię, wzmacniającą wizerunek Twojego wydarzenia.',
					},
					5: {
						title: 'PARTNER DLA WYMAGAJĄCYCH',
						desc: 'Agencje eventowe zyskują niezawodnego partnera rozumiejącego timing, layout i&nbsp;dynamikę dużych wydarzeń. Firmy, pary młode i&nbsp;organizatorzy prywatnych imprez otrzymują rozwiązanie premium: efekt „wow", emocje i&nbsp;pełną opiekę nad gośćmi. Właściciele lokali eventowych wzbogacają ofertę o&nbsp;mobilne stacje bez inwestycji w&nbsp;sprzęt – gotowe do działania w&nbsp;dowolnej przestrzeni.',
					},
					6: {
						title: 'NAPISZ DO NAS',
						desc: 'Twój event zasługuje na wyjątkowe live food station, które stanie się jego wizytówką. Napisz do nas już dziś - dopasujemy ofertę do Twojej wizji i&nbsp;zapewnimy termin. Razem stworzymy doświadczenie, które goście będą wspominać z&nbsp;zachwytem!',
					},
				},
			},
			faq: {
				title: 'FAQ - Najczęściej Zadawane Pytania',
				q1: {
					title: 'Czy jest ograniczona ilość porcji na osobę?',
					desc: 'Nie, nie ma żadnych limitów! Goście mogą sięgać po świeże porcje ile tylko chcą. Nasze live food station to obfitość smaków przygotowywanych na żywo.',
				},
				q2: {
					title: 'Czy można przedłużyć czas trwania usługi?',
					desc: 'Oczywiście! Elastyczność to nasza specjalność. Możesz przedłużyć usługę wcześniej, ustalając szczegóły, lub spontanicznie w trakcie eventu.',
				},
				q3: {
					title: 'W którym momencie wydarzenia najlepiej skorzystać ze stoiska Raricart?',
					desc: 'Wybór należy do Ciebie - my idealnie się dopasujemy! Najczęściej stawiamy stoiska jako atrakcję na początek, podczas przerwy koktajlowej lub na deserowy finisz.',
				},
				q4: {
					title: 'Jak zarezerwować usługę Raricart?',
					desc: 'To proste: skontaktuj się z nami przez formularz na stronie, e-mail lub telefon. Opowiedz o evencie, a w 24h prześlemy spersonalizowaną ofertę z menu i dostępnością. Rezerwacja z lekkim sercem!',
				},
				q5: {
					title: 'Co jest potrzebne, by Raricart pojawiło się na Twoim evencie?',
					desc: 'Tylko miejsce na nasze eleganckie stoisko (ok. 3x3m) i&nbsp;gniazdko prądu. Resztę załatwiamy my: dojazd, montaż, pełną obsługę, demontaż i&nbsp;sprzątanie. Zero zmartwień dla Ciebie.',
				},
				q6: {
					title: 'Jakie są ceny usług Raricart?',
					desc: 'Ceny są elastyczne i&nbsp;zależą od menu, liczby gości oraz czasu trwania – od 150 zł/os. wzwyż dla premium live stations. Wyślij zapytanie, a&nbsp;przygotujemy transparentną wycenę.',
				},
				q7: {
					title: 'Czy obsługujecie eventy plenerowe i&nbsp;bez kuchni na miejscu?',
					desc: 'Tak, jesteśmy mobilni na 100%! Dojedziemy wszędzie - na wesela w&nbsp;ogrodzie, firmowe pikniki czy gale pod chmurką. Bez zaplecza kuchennego? Żaden problem, nasze stoiska to kompletna, samodzielna magia kulinarna.',
				},
				q8: {
					title: 'Ile gości minimalnie obsługujecie?',
					desc: 'Nie ma minimum – realizujemy zlecenia na każdą skalę! Od kameralnych imprez prywatnych (20+ osób) po duże eventy (500+). Dla mniejszych grup skalujemy jedno eleganckie stoisko z&nbsp;pełnym efektem "wow". Przy większych imprezach zalecamy więcej niż jedno stoisko – to poprawia jakość obsługi, skraca czas oczekiwania i&nbsp;minimalizuje kolejki.',
				},
				q9: {
					title: 'Jak zapewniacie higienę i&nbsp;bezpieczeństwo?',
					desc: 'Jesteśmy certyfikowani (HACCP, Sanepid), z&nbsp;pełnym protokołem higieny na żywo. Świeże składniki, sterylne narzędzia i&nbsp;doświadczona obsługa.',
				},
			},
			contact: {
				title:
					'Zapytaj o dostępność terminu<br>i stwórzmy razem strefę smaku,<br>o której Twoi goście długo nie zapomną.',
			},
			form: {
				name: 'Imię i Nazwisko *',
				email: 'Email *',
				phone: 'Telefon *',
				date: 'Data Wydarzenia *',
				location: 'Lokalizacja Wydarzenia *',
				location_placeholder: 'np. Warszawa, Hotel Marriott',
				guests_label: 'Liczba Gości *',
				guests_placeholder: 'np. 80',
				budget: 'Budżet (PLN) *',
				budget_placeholder: 'np. 2000 albo 5000 do 10000',
				event_type: 'Rodzaj Wydarzenia *',
				select_placeholder: 'Wybierz...',
				types: {
					wedding: 'Wesele',
					corporate: 'Event Firmowy',
					festival: 'Festiwal/Piknik',
					private: 'Przyjęcie Prywatne',
					other: 'Inne',
				},
				stations: 'Interesujące Stacje *',
				st_pancakes: 'Mini Pancakes',
				st_icecream: 'Lody Włoskie',
				st_cheese: 'Deska Serów',
				contact_hours: 'Preferowane godziny kontaktu',
				contact_hours_placeholder: 'np. 10:00-14:00 lub po 18:00',
				message: 'Dodatkowe Informacje',
				submit: 'Wyślij Zapytanie',
				required: 'To pole jest wymagane',
				sending: 'Wysyłanie...',
				success_msg:
					'Szczegóły zapytania zostały przesłane. Potwierdzamy przyjęcie wiadomości. Skontaktujemy się z Państwem wkrótce w celu omówienia szczegółów.',
				error_msg: 'Błąd wysyłania. Sprawdź połączenie lub spróbuj później.',
				stations_error: 'Wybierz przynajmniej jedną stację',
				email_error: 'Nieprawidłowy adres email',
				progress_text: 'Uzupełnij dane, abyśmy mogli przygotować ofertę (0%)',
				message_placeholder: 'Opisz swoje potrzeby, pytania lub preferencje...',
			},
			cookies: {
				text: 'Ta strona używa plików cookies, aby zapewnić najlepszą jakość. Korzystając ze strony, zgadzasz się na ich użycie.',
				accept: 'Akceptuję',
				reject: 'Odrzuć',
			},
			footer: {
				desc: 'Mobilne stacje degustacyjne na eventy w całej Polsce.',
				phone: 'Telefon: <a href="tel:+48883392688" class="phone-link">+48 883 392 688</a>',
				quick_links: 'Szybkie Linki',
			},
			gallery: {
				badge: 'AUTENTYCZNE KADRY',
				title: 'Zobacz Raricart podczas wydarzeń',
				subtitle: 'Tak wygląda stacja, kiedy zaczyna się wydarzenie. Świeże produkty, przygotowanie na żywo, własne kompozycje i goście, którzy naprawdę chcą podejść do stacji.',
				see_more: 'ZOBACZ WIĘCEJ REALIZACJI',
			},
			reviews: {
				badge: 'GOOGLE REVIEWS &bull; 100% ZWERYFIKOWANE',
				title: 'Co mówią goście i organizatorzy?',
				subtitle: 'Prawdziwe emocje, puste talerzyki i spokój organizatora. Zobacz, jak wspominają stację Raricart pary młode, firmy i gospodarze przyjęć.',
				trust_summary: '<strong>5.0 / 5.0</strong> &bull; Ponad 120 zrealizowanych wydarzeń &bull; 100% zachwyconych gości',
				google_verified_meta: 'Wizytówka Google &bull; Ponad 120 obsłużonych wydarzeń',
				see_google_maps: 'Sprawdź w Google Maps',
				verified: 'Zweryfikowana realizacja',
				cta_title: 'Chcesz, aby Twoi goście również tak wspominali Twoje wydarzenie?',
				cta_desc: 'Napisz do nas lub zadzwoń. Sprawdzimy dostępność wybranej stacji w Twoim terminie w mniej niż 24 godziny.',
				cta_btn: 'ZAPYTAJ O WOLNY TERMIN',
				cta_google: 'OPINIE W GOOGLE MAPS'
			},
			modals: {
				pancakes: {
					title: 'Mini Pancakes',
					content: `<p class="modal-lead">Słodka stacja, która angażuje gości, przyciąga uwagę i&nbsp;naturalnie staje się jednym z&nbsp;najbardziej lubianych punktów wydarzenia.</p>
        <div class="modal-section"><h4>JAK TO DZIAŁA?</h4><p>Na oczach gości powstają delikatne, złociste pancakes: lekkie, puszyste i&nbsp;podawane w&nbsp;eleganckiej formie. Każda porcja przygotowywana jest na świeżo, dzięki czemu przestrzeń wypełnia przyjemny aromat, który natychmiast przyciąga uwagę.</p><p>Goście mogą samodzielnie stworzyć swoją kompozycję, wybierając spośród dodatków takich jak owoce, czekolada, posypki, polewy czy chrupiące ciasteczka. To moment swobody i&nbsp;kreatywności, który sprawia, że degustacja staje się przyjemnym doświadczeniem, a&nbsp;nie tylko deserem.</p></div>
        <div class="modal-section"><h4>DOBÓR DODATKÓW</h4><p>Jeśli wolisz, przejmiemy tę część za Ciebie: przygotujemy zestaw dodatków idealnie dopasowany do stylu wydarzenia i&nbsp;profilu gości. Zadbamy o&nbsp;harmonię smaków i&nbsp;estetykę prezentacji, tak by całość była spójna z&nbsp;charakterem wydarzenia i&nbsp;jego atmosferą.</p><p>Możesz także samodzielnie wybrać dodatki z&nbsp;naszej listy: damy Ci pełną swobodę w&nbsp;komponowaniu oferty według własnych preferencji.</p></div>
        <div class="modal-section"><h4>GDZIE SPRAWDZA SIĘ NASZA STACJA?</h4><ul><li>Eventy firmowe i&nbsp;konferencje</li><li>Wesela i&nbsp;przyjęcia weselne</li><li>Gale i&nbsp;bankiety</li><li>Prywatne przyjęcia i&nbsp;urodziny</li><li>Imprezy plenerowe i&nbsp;pikniki</li></ul></div>

        <div class="modal-section"><h4>CO OTRZYMUJESZ?</h4><ul><li>Profesjonalną, mobilną stację o&nbsp;dopracowanej estetyce</li><li>Obsługę od montażu po ostatni serwis</li><li>Świeżo przygotowane pancakes i&nbsp;starannie dobrane dodatki</li><li>Punkt, który angażuje gości i&nbsp;tworzy naturalne miejsce spotkań</li></ul></div>`,
				},
				icecream: {
					title: 'Lody Włoskie',
					content: `<p class="modal-lead">Orzeźwiająca stacja, która zachwyca gości, buduje atmosferę i&nbsp;naturalnie staje się hitem wydarzenia.</p>
        <div class="modal-section"><h4>JAK TO DZIAŁA?</h4><p>Goście mają okazję zobaczyć, jak kremowe lody włoskie nalewane są prosto z&nbsp;maszyny do eleganckich kubeczków: z&nbsp;aksamitną konsystencją i&nbsp;idealną świeżością. To proste, widowiskowe przedstawienie działa na zmysły i&nbsp;sprawia, że każdy deser jest wyjątkowy. Porcje serwowane są na bieżąco, zarówno na eventy plenerowe, jak i&nbsp;w&nbsp;przestrzeniach zamkniętych.</p><p>Do lodów dobieramy dodatki takie jak świeże owoce, sosy owocowe i&nbsp;czekoladowe, chrupiące posypki, orzechy czy mini ciasteczka, umożliwiając gościom stworzenie własnych kompozycji smakowych.</p></div>
        <div class="modal-section"><h4>DOBÓR SMAKÓW I&nbsp;DODATKÓW</h4><p>Możesz powierzyć nam dobór smaków i&nbsp;dodatków: dopasujemy konfigurację do charakteru wydarzenia, sezonu i&nbsp;profilu gości. Możliwa jest też pełna swoboda w&nbsp;samodzielnym skomponowaniu listy, tak by oferta idealnie wpasowała się w&nbsp;Twoją koncepcję.</p></div>
        <div class="modal-section"><h4>GDZIE SPRAWDZA SIĘ STACJA LODÓW?</h4><ul><li>Eventy firmowe i&nbsp;dni otwarte</li><li>Wesela, poprawiny i&nbsp;letnie przyjęcia</li><li>Gale, premiery, eventy wizerunkowe</li><li>Przyjęcia rodzinne, urodziny, komunie</li><li>Imprezy plenerowe i&nbsp;pikniki</li></ul></div>

        <div class="modal-section"><h4>CO ZYSKUJESZ?</h4><ul><li>Estetyczną, mobilną stację lodów dopasowaną do charakteru wydarzenia</li><li>Kompleksową obsługę – od przygotowania po serwis w&nbsp;trakcie eventu</li><li>Kremowe lody włoskie serwowane na żywo z&nbsp;profesjonalnie dobranymi dodatkami</li><li>Punkt, który naturalnie przyciąga gości i&nbsp;buduje pozytywne skojarzenia z&nbsp;wydarzeniem.</li></ul></div>`,
				},
				cheese: {
					title: 'Deska Serów',
					content: `<p class="modal-lead">Fascynująca strefa smaku, która podnosi wartość Twojego wydarzenia.</p>
        <div class="modal-section"><h4>JAK TO DZIAŁA?</h4><p>Nasze stoisko oferuje 12 starannie wyselekcjonowanych pozycji: wysokiej jakości sery i&nbsp;wędliny włoskie, uzupełnione wybornymi dodatkami słonymi. Wszystko to komponuje się w&nbsp;harmonijną całość, wzbogaconą o&nbsp;specjalnie dobrane sosy, które wzmacniają smak każdej degustacji. Goście sami tworzą swoje własne mini kompozycje desek serów, swobodnie sięgając po ulubione składniki: od kremowych serów z&nbsp;dojrzewającymi wędlinami, przez chrupiące dodatki, po wyrafinowane sosy podkreślające włoski charakter całości.</p></div>
        <div class="modal-section"><h4>STARANNIE SKOMPONOWANY DOBÓR SKŁADNIKÓW</h4><p>Dobór wszystkich 12 pozycji i&nbsp;sosów jest precyzyjnie przemyślany: każdy składnik został wyselekcjonowany tak, aby idealnie komponował się z&nbsp;pozostałymi, tworząc harmonijną całość smakową i&nbsp;wizualną. Kompozycja została stworzona z&nbsp;myślą o&nbsp;najwyższych standardach doświadczeń degustacyjnych, gwarantując gościom profesjonalne wrażenia na światowym poziomie. Powierzenie nam tego aspektu pozwala skupić się na organizacji wydarzenia, podczas gdy my zapewniamy spójność i&nbsp;jakość każdej porcji.</p></div>
        <div class="modal-section"><h4>GDZIE SPRAWDZA SIĘ STOISKO SERÓW?</h4><ul><li>Eventy firmowe, bankiety i&nbsp;koktajle</li><li>Wesela, przyjęcia przedślubne i&nbsp;poprawiny</li><li>Gale, wernisaże, premiery produktowe</li><li>Kameralne przyjęcia prywatne i&nbsp;spotkania biznesowe</li><li>Konferencje i&nbsp;networkingowe spotkania</li></ul></div>

        <div class="modal-section"><h4>CO ZYSKUJESZ?</h4><ul><li>Spektakularną ekspozycję kulinarną, która przyciąga wzrok</li><li>Wysokiej jakości produkty, które zadowolą koneserów</li><li>Alternatywę dla klasycznego bufetu – "grazing table" w&nbsp;wersji premium</li><li>Elegancki element, który podnosi rangę wydarzenia</li></ul></div>`,
				},
			},
		},
		en: {
			nav: {
				about: 'About Us',
				offer: 'Offer',
				gallery: 'Gallery',
				faq: 'FAQ',
				why_us: 'Why Us',
				contact: 'Contact',
				check_date: 'Check Date',
				packages: 'Packages',
			},
			hero: {
				title1: 'WHERE TASTE MEETS EMOTION AND SIMPLICITY BECOMES ELEGANCE...',
				title2: '...THAT\'S WHERE <span class="logo-pulse">RARICART</span> BEGINS',
				scroll: 'Scroll Down',
			},
			trust_bar: {
				badge: 'Culinary Experience',
				headline: 'Food that happens right in front of your guests.',
				pillar1_title: 'FRESHLY PREPARED',
				pillar1_desc: 'Every portion is prepared live before your guests\' eyes.',
				pillar2_title: 'CUSTOM COMPOSITIONS',
				pillar2_desc: 'Guests choose their favorite toppings and flavor combinations.',
				pillar3_title: 'FULL SERVICE',
				pillar3_desc: 'From setup and live serving to breakdown and spotless cleanup.',
				pillar4_title: 'MOBILE STATIONS',
				pillar4_desc: 'Operating anywhere: outdoors, elegant banquet halls, offices.',
			},
			stations: {
				badge: 'What We Offer',
				title: 'Choose Your Station',
				intro: "We aren't a traditional buffet. Our stations prepare food live on-site, right before your guests' eyes. Everyone can choose their own toppings and create a personalized composition.",
				see_all: 'EXPLORE FULL OFFER & PACKAGES',
				explore_btn: 'EXPLORE STATION',
				badge_sweet: 'Sweet Treat',
				badge_refresh: 'Refreshing',
				badge_savory: 'Savory',
				pancakes_desc: 'Warm, fluffy mini pancakes with toppings and sauces prepared right before your guests.',
				icecream_desc: 'Creamy soft serve ice cream, fresh fruit, artisan syrups, and crunchy toppings.',
				cheese_desc: 'Carefully curated artisan cheeses, cured meats, and savory accompaniments in an elegant setup.',
				combine_note: '<strong>Looking for more than one station?</strong> You can combine multiple stations at your event — we will tailor an exclusive package for you.',
				cta_check: 'CHECK STATION AVAILABILITY',
			},
			why_station: {
				stat_label: 'Live & Fresh',
				badge: 'Why a food station instead of catering?',
				title: 'More than food. An attraction for your guests.',
				intro: 'Traditional buffet catering often sits in chafing dishes waiting for guests. We create a live culinary experience that engages the senses and becomes a natural social centerpiece at your celebration.',
				pillar1_title: 'TASTE',
				pillar1_desc: 'Fresh ingredients prepared and served live right before your guests — zero compromises, zero reheating.',
				pillar2_title: 'EXPERIENCE',
				pillar2_desc: 'Guests enthusiastically watch the preparation process, interact with our chefs, and compose their own treats.',
				pillar3_title: 'AESTHETICS',
				pillar3_desc: 'Our refined, photogenic mobile setup seamlessly blends into the styling and decor of your venue or garden.',
				benefit_box_label: 'Hassle-Free Organization',
				benefit_box_p1: 'And you? You don’t need to coordinate service staff, arrange kitchen space, or worry about cleanup.',
				benefit_box_rhythm: 'We arrive. We prepare. We serve. We clean up.',
				benefit_box_p2: 'You focus on your guests. We take care of the entire station.',
				cta_btn: 'I WANT THIS STATION AT MY EVENT',
			},
			process: {
				badge: 'SIMPLE WORKFLOW',
				title: 'From the first message to the last bite.',
				subtitle: 'Zero complicated formalities and zero stress. See how Raricart stations are executed step by step at your wedding, corporate event, or private party.',
				step1_tag: 'Inquiry',
				step1_title: 'You tell us about your event',
				step1_desc: 'Share your date, location, estimated guest count, and event style. Just a quick message through our contact form.',
				step2_tag: 'Tailoring',
				step2_title: 'We recommend the perfect setup',
				step2_desc: 'We advise which station or combo best suits your party format, schedule, and guest profile.',
				step3_tag: 'Full Logistics',
				step3_title: 'We arrive and set up everything',
				step3_desc: 'Transport, setup, equipment, premium ingredients, and trained staff — 100% of preparation is handled by us.',
				step4_tag: 'Live Cooking',
				step4_title: 'Guests enjoy the live station',
				step4_desc: 'Guests pick toppings, build custom flavors, and return for more. The station is buzzing with smiles, energy, and aroma.',
				step5_tag: 'Spotless Finish',
				step5_title: 'We clean up and pack away',
				step5_desc: 'Once service concludes, we smoothly dismantle the station and leave the venue sparkling clean. You have zero worries.',
				summary_highlight: 'It couldn’t be simpler.',
				summary_lead: 'You enjoy your guests and the celebration — we ensure culinary delight and seamless service from start to finish.',
				microcopy: '⏱️ We typically reply to inquiries within a few hours.',
				cta_btn: 'CHECK AVAILABILITY',
			},
			audiences: {
				badge: 'WHO IS RARICART FOR?',
				title: 'Where does Raricart appear?',
				subtitle: 'We tailor each station to the style and schedule of your celebration. Discover the formats where we shine brightest.',
				card1_tag: 'Weddings & Next-Day Parties',
				card1_title: 'Weddings with character',
				card1_desc: 'A live sweet attraction, a refreshing dessert after dinner, late-night bites after dancing, or an elegant chillout zone.',
				card1_f1: 'Modern alternative to a static candy bar',
				card1_f2: 'A lively spot where guests chat and snap photos',
				card1_f3: 'Smooth service with zero queues and unlimited portions',
				card2_tag: 'Corporate & Business',
				card2_title: 'Corporate events and galas',
				card2_desc: 'Team retreats, corporate picnics, conferences, company anniversaries, galas, and product launches.',
				card2_f1: 'High capacity (serving from 50 up to 500+ guests)',
				card2_f2: 'Full VAT invoices, insurance, and hygiene certifications',
				card2_f3: 'Custom menu and station branding options',
				card3_tag: 'Private Celebrations',
				card3_title: 'Birthdays and garden parties',
				card3_desc: 'Milestone birthdays, anniversaries, baby showers, family reunions, and casual parties in your garden or rented venue.',
				card3_f1: 'Compact footprint — fits on patios, decks, or terraces',
				card3_f2: 'A treat for kids and delight for discerning foodies',
				card3_f3: 'You spend time with loved ones, we handle all catering',
				card4_tag: 'Agencies & B2B Partners',
				card4_title: 'Event agencies & Wedding planners',
				card4_desc: 'A reliable, aesthetic catering partner ready to plug smoothly into your overall event timeline and production.',
				card4_f1: 'Rock-solid punctuality and discreet, quiet setup',
				card4_f2: 'Impeccable staff dress code and high personal culture',
				card4_f3: 'Total peace of mind — 100% accountability for our zone',
				footer_title: 'Planning something unique?',
				footer_desc: 'Drop us a line. We gladly adapt stations, menus, and logistics to any bespoke event format.',
				cta_btn: 'CONSULT YOUR EVENT',
			},
			why_raricart: {
				badge: '6 CORE ADVANTAGES',
				title: 'Why choose Raricart?',
				subtitle: "We aren't a traditional chafing-dish buffet. Here are 6 reasons why guests remember our live food stations long after the party.",
				card1_title: 'Live Freshness',
				card1_desc: 'Every single portion is crafted on-site before your guests’ eyes. Warm pancakes fresh off the griddle, freshly churned ice cream, and fragrant toppings — never reheated.',
				card2_title: 'Freedom of Choice',
				card2_desc: 'Guests build their own creations: artisan sauces, fresh berries, crunchy crumbles, and gourmet toppings. Everyone gets exactly what they crave.',
				card3_title: 'WOW Factor & Bonding',
				card3_desc: 'Live cooking captivates guests, fills the room with enticing aromas, and creates an organic gathering hub for lively conversations and photos.',
				card4_title: 'Aesthetic in Every Detail',
				card4_desc: 'Our mobile stations are refined event pieces, handcrafted to elevate your venue’s aesthetics rather than looking like an ad-hoc food table.',
				card5_title: 'Organizer Peace of Mind',
				card5_desc: 'Delivery, setup, gear, professional staff, active service, and spotless cleanup — 100% on us. You focus entirely on your guests.',
				card6_title: 'Total Scalability',
				card6_desc: 'A single station or a multi-station tasting package? We adapt menus, service timing, and setup to any event scale from 20 to 500+ guests.',
				cta_btn: 'CHECK AVAILABILITY FOR YOUR EVENT',
			},
			about: {
				title: "We don't create catering, but an experience.",
				p1: 'We create mobile tasting stations that become the highlight of every event. It\'s not just food, it\'s a <span class="highlight">subtle, refined, and full of character</span> scenic element.',
				p2: 'We serve light, fresh compositions — from fluffy mini pancakes, through authentic Italian ice cream, to aromatic cheese boards. We build an atmosphere of class and freedom where your guests feel special.',
			},
			offer: {
				title: 'Our Offer',
				intro_title: "WE DON'T JUST COOK, WE CREATE MOMENTS THAT CONNECT PEOPLE",
				p1: 'Our stations become places for conversation, smiles, and photos, while we take care of every detail, from setup to the last service.',
				p2: 'We serve corporate events, weddings, galas,<br> and private parties, working with both agencies and individual clients.',
				p3: 'In every project, we believe that taste and aesthetics have equal value—together they create an unforgettable atmosphere. With Raricart, you get a cohesive, beautiful scenic element that tastes as good as it looks.',
				cards: {
					pancakes: {
						title: 'Mini Pancakes',
						desc: 'A sweet station that engages guests and becomes the heart of the event.',
					},
					icecream: {
						title: 'Soft Serve Ice Cream',
						desc: 'A refreshing station that delights guests and builds atmosphere.',
					},
					cheese: { title: 'Cheese Board', desc: 'A fascinating taste zone with Italian cheeses and cold cuts.' },
				},
			},
			why: {
				title: 'THE ART OF CULINARY EXPERIENCES',
				cards: {
					1: {
						title: "CLASSY 'WOW' EFFECT",
						desc: "We serve warm pancakes right from the griddle, pour creamy ice cream, and compose cheese boards right before guests' eyes - everything fresh, personalized, and refined in detail. The live station process attracts attention, integrates participants, and creates natural meeting points where conversations are born over an appetizing view. The elegant, mobile station design raises the prestige of the event, combining premium aesthetics with pure pleasure for the senses.",
					},
					2: {
						title: 'LESS LOGISTICS, MORE PEACE',
						desc: "Raricart takes over everything: travel, station assembly, service during the event, disassembly, and perfect cleanup afterwards. We don't need kitchen facilities - our mobile stations work everywhere: in lofts, gardens, halls, or unusual event spaces. The team synchronizes the service with the schedule, ensures smooth guest flow, and minimizes queues.",
					},
					3: {
						title: 'EXPERIENCE INSTEAD OF BUFFET',
						desc: "Unlike a static buffet, our live stations engage: guests watch the pouring of ice cream, stacking of pancakes, and composing of cheese boards, choosing toppings on the fly. Everything is served in portions 'here and now' - fresh, without waste, perfectly matched to the number and preferences of participants. Themed stations become a magnet for guests, building emotions and unforgettable memories.",
					},
					4: {
						title: 'SAFETY, QUALITY, AESTHETICS',
						desc: "We adhere to strict hygiene and food safety standards, with an emphasis on ingredient freshness and perfect presentation. We use selected products served at optimal temperatures. Every detail - from station arrangement, through tableware, to team work - creates a cohesive scenography that strengthens your event's image.",
					},
					5: {
						title: 'PARTNER FOR THE DEMANDING',
						desc: "Event agencies gain a reliable partner who understands timing, layout, and the dynamics of large events. Companies, couples, and private party organizers receive a premium solution: a 'wow' effect, emotions, and full care for guests. Event venue owners enrich their offer with mobile stations without investing in equipment – ready to operate in any space.",
					},
					6: {
						title: 'WRITE TO US',
						desc: 'Your event deserves a unique live food station that will become its showcase. Write to us today - we will tailor the offer to your vision and secure the date. Together we will create an experience that guests will remember with delight!',
					},
				},
			},
			faq: {
				title: 'FAQ - Frequently Asked Questions',
				q1: {
					title: 'Is there a limit on portions per person?',
					desc: 'No, there are no limits! Guests can reach for fresh portions as much as they want. Our live food station is an abundance of flavors prepared live.',
				},
				q2: {
					title: 'Can the service duration be extended?',
					desc: 'Of course! Flexibility is our specialty. You can extend the service in advance by arranging details, or spontaneously during the event.',
				},
				q3: {
					title: 'At what moment of the event is it best to use the Raricart stand?',
					desc: 'The choice is yours - we will fit in perfectly! We most often set up stands as an attraction at the beginning, during a cocktail break, or as a dessert finish.',
				},
				q4: {
					title: 'How to book Raricart service?',
					desc: "It's simple: contact us via the form on the website, email, or phone. Tell us about the event, and within 24h we will send a personalized offer with menu and availability. Booking with a light heart!",
				},
				q5: {
					title: 'What is needed for Raricart to appear at your event?',
					desc: 'Only space for our elegant stand (approx. 3x3m) and a power outlet. We handle the rest: transport, assembly, full service, disassembly, and cleaning. Zero worries for you.',
				},
				q6: {
					title: 'What are the prices of Raricart services?',
					desc: 'Prices are flexible and depend on the menu, number of guests, and duration – from 150 PLN/person upwards for premium live stations. Send an inquiry, and we will prepare a transparent quote.',
				},
				q7: {
					title: 'Do you serve outdoor events and those without a kitchen on site?',
					desc: 'Yes, we are 100% mobile! We will get anywhere - to garden weddings, corporate picnics, or open-air galas. No kitchen facilities? No problem, our stands are complete, independent culinary magic.',
				},
				q8: {
					title: 'What is the minimum number of guests you serve?',
					desc: 'There is no minimum – we carry out orders on any scale! From intimate private parties (20+ people) to large events (500+). For smaller groups, we scale one elegant stand with a full "wow" effect. For larger events, we recommend more than one stand – this improves service quality, shortens waiting time, and minimizes queues.',
				},
				q9: {
					title: 'How do you ensure hygiene and safety?',
					desc: 'We are certified (HACCP, Sanepid), with a full live hygiene protocol. Fresh ingredients, sterile tools, and experienced service.',
				},
			},
			contact: {
				title: "Ask for availability<br>and let's create a taste zone together<br>that your guests will not forget.",
			},
			form: {
				name: 'Name & Surname *',
				email: 'Email *',
				phone: 'Phone *',
				date: 'Event Date *',
				location: 'Event Location *',
				location_placeholder: 'e.g. Warsaw, Marriott Hotel',
				guests_label: 'Number of Guests *',
				guests_placeholder: 'e.g. 80',
				budget: 'Budget (PLN) *',
				budget_placeholder: 'e.g. 2000 or 5000 to 10000',
				event_type: 'Event Type *',
				select_placeholder: 'Choose...',
				types: {
					wedding: 'Wedding',
					corporate: 'Corporate Event',
					festival: 'Festival/Picnic',
					private: 'Private Party',
					other: 'Other',
				},
				stations: 'Interested Stations *',
				st_pancakes: 'Mini Pancakes',
				st_icecream: 'Soft Serve Ice Cream',
				st_cheese: 'Cheese Board',
				contact_hours: 'Preferred contact hours',
				contact_hours_placeholder: 'e.g. 10:00-14:00 or after 18:00',
				message: 'Additional Information',
				submit: 'Send Query',
				required: 'This field is required',
				stations_error: 'Select at least one station',
				email_error: 'Invalid email address',
				sending: 'Sending...',
				success_msg:
					'Inquiry details have been sent. We confirm receipt of the message. We will contact you shortly to discuss details.',
				error_msg: 'Error sending. Check your connection or try again later.',
				progress_text: 'Complete the data so we can prepare an offer (0%)',
				message_placeholder: 'Describe your needs, questions or preferences...',
			},
			cookies: {
				text: 'This site uses cookies to ensure the best quality. By using the site, you agree to their use.',
				accept: 'Accept',
				reject: 'Reject',
			},
			footer: {
				desc: 'Mobile tasting stations for events all over Poland.',
				phone: 'Phone: <a href="tel:+48883392688" class="phone-link">+48 883 392 688</a>',
				quick_links: 'Quick Links',
			},
			gallery: {
				badge: 'AUTHENTIC MOMENTS',
				title: 'See Raricart in action at real events',
				subtitle: 'This is what the station looks like when the celebration begins: fresh ingredients, live cooking, custom toppings, and guests eager to step right up.',
				see_more: 'VIEW FULL GALLERY',
			},
			reviews: {
				badge: 'GOOGLE REVIEWS &bull; 100% VERIFIED',
				title: 'What guests and event hosts say',
				subtitle: 'Authentic excitement, empty plates, and total peace of mind for the organizer. Here is how couples, corporate managers, and private hosts recall Raricart.',
				trust_summary: '<strong>5.0 / 5.0</strong> &bull; Over 120 events hosted &bull; 100% delighted guests',
				google_verified_meta: 'Google Business Profile &bull; Over 120 events hosted',
				see_google_maps: 'Check on Google Maps',
				verified: 'Verified booking',
				cta_title: 'Want your guests to remember your event like this?',
				cta_desc: 'Get in touch. We will verify station availability for your date in under 24 hours.',
				cta_btn: 'CHECK DATE AVAILABILITY',
				cta_google: 'REVIEWS ON GOOGLE MAPS'
			},
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
				},
			},
		},
		es: {
			nav: {
				about: 'Sobre Nosotros',
				offer: 'Oferta',
				gallery: 'Galería',
				faq: 'FAQ',
				why_us: 'Por Qué Nosotros',
				contact: 'Contacto',
				check_date: 'Consultar Fecha',
				packages: 'Paquetes',
			},
			hero: {
				title1: 'DONDE EL SABOR SE ENCUENTRA CON LA EMOCIÓN Y LA SIMPLICIDAD SE VUELVE ELEGANCIA...',
				title2: '...AHÍ ES DONDE COMIENZA RARICART',
				scroll: 'Desplácese hacia abajo',
			},
			trust_bar: {
				badge: 'Experiencia Culinaria',
				headline: 'Comida que sucede ante los ojos de los invitados.',
				pillar1_title: 'RECIÉN PREPARADO',
				pillar1_desc: 'Cada porción se elabora en vivo ante los ojos de tus invitados.',
				pillar2_title: 'COMPOSICIONES PROPIAS',
				pillar2_desc: 'Los invitados eligen sus aderezos y sabores favoritos.',
				pillar3_title: 'SERVICIO COMPLETO',
				pillar3_desc: 'Desde el montaje y servicio hasta el desmontaje y orden impecable.',
				pillar4_title: 'ESTACIONES MÓVILES',
				pillar4_desc: 'Operamos en cualquier espacio: exteriores, salones, oficinas.',
			},
			stations: {
				badge: '¿Qué ofrecemos?',
				title: 'Elige tu estación',
				intro: 'No somos un buffet tradicional. Nuestras estaciones preparan la comida en vivo, ante los ojos de los invitados. Cada uno elige sus propios aderezos y crea su combinación favorita.',
				see_all: 'VER OFERTA COMPLETA Y PAQUETES',
				explore_btn: 'CONOCER ESTACIÓN',
				badge_sweet: 'Dulce',
				badge_refresh: 'Refrescante',
				badge_savory: 'Salado',
				pancakes_desc: 'Mini pancakes calientes y esponjosos preparados al momento ante los ojos de los invitados.',
				icecream_desc: 'Cremoso helado italiano, fruta fresca, salsas artesanales y toppings crujientes.',
				cheese_desc: 'Quesos selectos, embutidos y aderezos salados en una presentación elegante.',
				combine_note: '<strong>¿Quieres más de una estación?</strong> Puedes combinar las estaciones que desees — prepararemos un paquete personalizado para tu evento.',
				cta_check: 'CONSULTAR DISPONIBILIDAD',
			},
			why_station: {
				stat_label: 'En vivo y fresco',
				badge: '¿Por qué una estación en lugar de catering?',
				title: 'No solo comida. Una atracción para tus invitados.',
				intro: 'El catering tradicional suele esperar en bandejas calientes. Nosotros creamos una experiencia culinaria en vivo que involucra los sentidos y se convierte en el centro natural del evento.',
				pillar1_title: 'SABOR',
				pillar1_desc: 'Productos frescos elaborados y servidos al momento ante los ojos de los invitados, sin recalentar.',
				pillar2_title: 'EXPERIENCIA',
				pillar2_desc: 'Los invitados observan el proceso con deleite, interactúan con nuestro equipo y eligen sus combinaciones favoritas.',
				pillar3_title: 'ESTÉTICA',
				pillar3_desc: 'Una estación móvil refinada y fotogénica que forma parte de la escenografía y ambientación de tu celebración.',
				benefit_box_label: 'Organización sin estrés',
				benefit_box_p1: '¿Y tú? No necesitas coordinar camareros, montar el puesto ni preocuparte por la limpieza.',
				benefit_box_rhythm: 'Llegamos. Preparamos. Servimos. Limpiamos.',
				benefit_box_p2: 'Tú te dedicas a tus invitados. Nosotros nos ocupamos de toda la estación.',
				cta_btn: 'QUIERO ESTA ESTACIÓN EN MI EVENTO',
			},
			process: {
				badge: 'PROCESO SENCILLO',
				title: 'Desde el primer mensaje hasta la última porción.',
				subtitle: 'Sin trámites complicados ni estrés. Descubre cómo se realiza paso a paso una estación Raricart en tu boda, evento corporativo o fiesta privada.',
				step1_tag: 'Contacto',
				step1_title: 'Nos cuentas sobre tu evento',
				step1_desc: 'Indica la fecha, lugar, número estimado de invitados y estilo del evento. Solo toma un minuto en nuestro formulario.',
				step2_tag: 'Personalización',
				step2_title: 'Elegimos la estación perfecta',
				step2_desc: 'Te asesoramos sobre la mejor opción o combinación de estaciones según el formato de tu fiesta y horario.',
				step3_tag: 'Logística 100%',
				step3_title: 'Llegamos y preparamos todo',
				step3_desc: 'Transporte, montaje, equipamiento, ingredientes frescos y personal cualificado: todo corre por nuestra cuenta.',
				step4_tag: 'Live Cooking',
				step4_title: 'Los invitados disfrutan la estación',
				step4_desc: 'Eligen ingredientes, crean combinaciones al momento y repiten cuando quieran. La estación se llena de vida y sonrisas.',
				step5_tag: 'Limpieza total',
				step5_title: 'Recogemos y limpiamos todo',
				step5_desc: 'Al terminar el servicio desmontamos la estación y dejamos el espacio impecable. Tú no te preocupas por nada.',
				summary_highlight: 'Más fácil imposible.',
				summary_lead: 'Tú disfrutas con tus invitados: nosotros nos ocupamos de deleitarlos con un servicio impecable de principio a fin.',
				microcopy: '⏱️ Solemos responder a las consultas en pocas horas.',
				cta_btn: 'CONSULTAR DISPONIBILIDAD',
			},
			audiences: {
				badge: '¿PARA QUIÉN ES RARICART?',
				title: '¿Dónde aparece Raricart?',
				subtitle: 'Adaptamos la estación al estilo y horario de tu celebración. Descubre en qué formatos destacamos y encuentra el tuyo.',
				card1_tag: 'Bodas y Celebraciones',
				card1_title: 'Bodas con carácter',
				card1_desc: 'Una atracción dulce en vivo, un postre refrescante tras la comida, aperitivos de madrugada o una zona chillout con estilo.',
				card1_f1: 'Alternativa moderna y dinámica a la mesa de dulces',
				card1_f2: 'Punto de encuentro donde los invitados conversan y toman fotos',
				card1_f3: 'Servicio fluido sin colas y con porciones ilimitadas',
				card2_tag: 'Empresas y Negocios',
				card2_title: 'Eventos corporativos y galas',
				card2_desc: 'Jornadas de integración, picnics de empresa, conferencias, aniversarios corporativos y lanzamientos de productos.',
				card2_f1: 'Alta capacidad operativa (desde 50 hasta más de 500 personas)',
				card2_f2: 'Factura con IVA, seguros y normativas sanitarias al día',
				card2_f3: 'Posibilidad de personalización de menú y marca',
				card3_tag: 'Fiestas Privadas',
				card3_title: 'Cumpleaños y fiestas en jardín',
				card3_desc: 'Cumpleaños especiales, aniversarios, baby showers, bautizos y reuniones relajadas en jardines o terrazas privadas.',
				card3_f1: 'Dimensiones compactas: encajamos en terrazas o salones',
				card3_f2: 'Alegría para niños y deleite para adultos',
				card3_f3: 'Tú disfrutas con tu familia, nosotros nos encargamos de todo',
				card4_tag: 'Agencias y Wedding Planners',
				card4_title: 'Agencias y Wedding Planners',
				card4_desc: 'Un proveedor gastronómico confiable y estético listo para integrarse a la perfección en el guion de tu evento.',
				card4_f1: 'Puntualidad rigurosa y montaje rápido y silencioso',
				card4_f2: 'Código de vestimenta impecable y trato exquisito',
				card4_f3: 'Tranquilidad para el organizador: asumimos 100% de la estación',
				footer_title: '¿Organizas algo diferente?',
				footer_desc: 'Escríbenos. Adaptamos con gusto la estación, el menú y la logística a cualquier formato fuera de lo común.',
				cta_btn: 'CONSULTAR TU EVENTO',
			},
			why_raricart: {
				badge: '6 GRANDES VENTAJAS',
				title: '¿Por qué elegir Raricart?',
				subtitle: 'No somos un buffet estático tradicional. Descubre las 6 razones por las que los invitados recuerdan nuestras estaciones en vivo.',
				card1_title: 'Frescura al momento',
				card1_desc: 'Cada porción se elabora en directo ante los ojos de tus invitados. Pancakes calientes al momento, helados cremosos e ingredientes frescos sin recalentar.',
				card2_title: 'Libertad de elección',
				card2_desc: 'Los invitados diseñan su propia combinación: salsas artesanales, frutas frescas, crujientes toppings y sabores gourmet.',
				card3_title: 'Efecto WOW e integración',
				card3_desc: 'La preparación en vivo atrae miradas, despierta el apetito con sus aromas y crea un punto de encuentro natural para conversar y tomar fotos.',
				card4_title: 'Estética en cada detalle',
				card4_desc: 'Nuestras estaciones son módulos elegantes que se integran en la decoración de tu salón o jardín como un elemento de alta gama.',
				card5_title: 'Comodidad absoluta',
				card5_desc: 'Transporte, montaje, equipamiento, servicio y recogida impecable: todo bajo nuestro control. Tú solo disfrutas de la fiesta.',
				card6_title: 'Flexibilidad total',
				card6_desc: '¿Una estación o un paquete combinado? Adaptamos la propuesta, horarios y capacidades para eventos desde 20 hasta más de 500 personas.',
				cta_btn: 'CONSULTAR DISPONIBILIDAD PARA TU EVENTO',
			},
			about: {
				title:
					'NO CREAMOS CATERING, SINO UNA EXPERIENCIA QUE DELEITA LOS OJOS, INVOLUCRA LOS SENTIDOS Y PERMANECE EN LA MEMORIA POR MUCHO TIEMPO',
				p1: 'Creamos estaciones de degustación móviles que se convierten en el punto culminante de cada evento: sutiles, refinadas y llenas de carácter.',
				p2: 'Servimos composiciones ligeras y frescas, desde mini pancakes <br>y helados italianos hasta aromáticas tablas de quesos, creando una atmósfera donde los invitados se sienten relajados y especiales.',
			},
			offer: {
				title: 'Nuestra Oferta',
				intro_title: 'NO SOLO COCINAMOS, CREAMOS MOMENTOS QUE CONECTAN A LAS PERSONAS',
				p1: 'Nuestras estaciones se convierten en lugares para conversar, sonreír y tomar fotos, mientras nosotros nos ocupamos de cada detalle, desde el montaje hasta el último servicio, para que puedas disfrutar del evento tanto como tus invitados.',
				p2: 'Atendemos eventos corporativos, bodas, galas<br> y fiestas privadas, trabajando tanto con agencias como con clientes individuales.',
				p3: 'En cada proyecto, nos guiamos por el principio de que el sabor y la estética tienen el mismo valor: juntos crean una atmósfera inolvidable. Con Raricart obtienes no solo catering, sino un elemento escénico coherente y hermoso de tu evento, que sabe tan bien como se ve.',
				cards: {
					pancakes: {
						title: 'Mini Pancakes',
						desc: 'Una estación dulce que atrae a los invitados y se convierte en el corazón del evento.',
					},
					icecream: {
						title: 'Helado Suave',
						desc: 'Una estación refrescante que deleita a los invitados y crea ambiente.',
					},
					cheese: { title: 'Tabla de Quesos', desc: 'Una fascinante zona de sabor con quesos y embutidos italianos.' },
				},
			},
			why: {
				title: 'EL ARTE DE LAS EXPERIENCIAS CULINARIAS',
				cards: {
					1: {
						title: "EFECTO 'WOW' CON CLASE",
						desc: 'Servimos pancakes calientes directamente de la plancha, vertemos helado cremoso y componemos tablas de quesos ante los ojos de los invitados: todo fresco, personalizado y refinado en cada detalle. El proceso de estación en vivo atrae la atención, integra a los participantes y crea puntos de encuentro naturales, donde nacen conversaciones ante una vista apetecible. El diseño elegante y móvil de la estación eleva el prestigio del evento, combinando estética premium con puro placer para los sentidos.',
					},
					2: {
						title: 'MENOS LOGÍSTICA, MÁS PAZ',
						desc: 'Raricart se encarga de todo: viaje, montaje de la estación, servicio durante el evento, desmontaje y limpieza perfecta después. No necesitamos instalaciones de cocina: nuestras estaciones móviles funcionan en todas partes: en lofts, jardines, salones o espacios para eventos inusuales. El equipo sincroniza el servicio con el horario, asegura un flujo fluido de invitados y minimiza las colas.',
					},
					3: {
						title: 'EXPERIENCIA EN LUGAR DE BUFFET',
						desc: "A diferencia de un buffet estático, nuestras estaciones en vivo involucran: los invitados observan cómo se sirve el helado, se apilan los pancakes y se componen las tablas de quesos, eligiendo aderezos sobre la marcha. Todo se sirve en porciones 'aquí y ahora': fresco, sin desperdicios, perfectamente adaptado al número y preferencias de los participantes. Las estaciones temáticas se convierten en un imán para los invitados, construyendo emociones y recuerdos inolvidables.",
					},
					4: {
						title: 'SEGURIDAD, CALIDAD, ESTÉTICA',
						desc: 'Cumplimos con estrictos estándares de higiene y seguridad alimentaria, con énfasis en la frescura de los ingredientes y una presentación perfecta. Utilizamos productos seleccionados servidos a temperaturas óptimas. Cada detalle, desde la disposición de la estación, pasando por la vajilla, hasta el trabajo del equipo, crea una escenografía coherente que fortalece la imagen de tu evento.',
					},
					5: {
						title: 'SOCIO PARA LOS EXIGENTES',
						desc: "Las agencias de eventos obtienen un socio confiable que comprende los tiempos, el diseño y la dinámica de los grandes eventos. Empresas, parejas y organizadores de fiestas privadas reciben una solución premium: efecto 'wow', emociones y cuidado total de los invitados. Los propietarios de locales para eventos enriquecen su oferta con estaciones móviles sin invertir en equipos, listos para operar en cualquier espacio.",
					},
					6: {
						title: 'ESCRIBENOS',
						desc: 'Tu evento merece una estación de comida en vivo única que se convierta en su escaparate. Escríbenos hoy: adaptaremos la oferta a tu visión y aseguraremos la fecha. ¡Juntos crearemos una experiencia que los invitados recordarán con deleite!',
					},
				},
			},
			faq: {
				title: 'FAQ - Preguntas Frecuentes',
				q1: {
					title: '¿Hay un límite de porciones por persona?',
					desc: '¡No, no hay límites! Los invitados pueden servirse porciones frescas tanto como quieran. Nuestra estación de comida en vivo es una abundancia de sabores preparados al momento.',
				},
				q2: {
					title: '¿Se puede extender la duración del servicio?',
					desc: '¡Por supuesto! La flexibilidad es nuestra especialidad. Puede extender el servicio con anticipación acordando los detalles, o espontáneamente durante el evento.',
				},
				q3: {
					title: '¿En qué momento del evento es mejor utilizar el puesto de Raricart?',
					desc: 'La elección es suya: ¡nos adaptaremos perfectamente! Con mayor frecuencia instalamos puestos como atracción al principio, durante un cóctel o como broche final de postre.',
				},
				q4: {
					title: '¿Cómo reservar el servicio de Raricart?',
					desc: 'Es simple: contáctenos a través del formulario en el sitio web, correo electrónico o teléfono. Cuéntenos sobre el evento y en 24h le enviaremos una oferta personalizada con menú y disponibilidad. ¡Reserva con el corazón ligero!',
				},
				q5: {
					title: '¿Qué se necesita para que Raricart aparezca en su evento?',
					desc: 'Solo espacio para nuestro elegante puesto (aprox. 3x3m) y una toma de corriente. Nosotros nos encargamos del resto: transporte, montaje, servicio completo, desmontaje y limpieza. Cero preocupaciones para usted.',
				},
				q6: {
					title: '¿Cuáles son los precios de los servicios de Raricart?',
					desc: 'Los precios son flexibles y dependen del menú, el número de invitados y la duración: desde 150 PLN/persona en adelante para estaciones en vivo premium. Envíe una consulta y prepararemos un presupuesto transparente.',
				},
				q7: {
					title: '¿Atienden eventos al aire libre y sin cocina en el lugar?',
					desc: '¡Sí, somos 100% móviles! Llegaremos a cualquier lugar: bodas en el jardín, picnics corporativos o galas al aire libre. ¿Sin instalaciones de cocina? No hay problema, nuestros puestos son magia culinaria completa e independiente.',
				},
				q8: {
					title: '¿Cuál es el número mínimo de invitados que atienden?',
					desc: 'No hay mínimo: ¡realizamos pedidos a cualquier escala! Desde fiestas privadas íntimas (20+ personas) hasta grandes eventos (500+). Para grupos más pequeños, escalamos un puesto elegante con un efecto "wow" completo. Para eventos más grandes, recomendamos más de un puesto: esto mejora la calidad del servicio, acorta el tiempo de espera y minimiza las colas.',
				},
				q9: {
					title: '¿Cómo garantizan la higiene y la seguridad?',
					desc: 'Estamos certificados (HACCP, Sanepid), con un protocolo completo de higiene en vivo. Ingredientes frescos, herramientas estériles y servicio experimentado.',
				},
			},
			contact: {
				title: 'Pregunte por disponibilidad<br>y creemos juntos una zona de sabor<br>que sus invitados no olvidarán.',
			},
			form: {
				name: 'Nombre y Apellidos *',
				email: 'Email *',
				phone: 'Teléfono *',
				date: 'Fecha del Evento *',
				location: 'Ubicación del Evento *',
				location_placeholder: 'ej. Varsovia, Hotel Marriott',
				guests_label: 'Número de Invitados *',
				guests_placeholder: 'ej. 80',
				budget: 'Presupuesto (PLN) *',
				budget_placeholder: 'ej. 2000 o 5000 a 10000',
				event_type: 'Tipo de Evento *',
				select_placeholder: 'Seleccionar...',
				types: {
					wedding: 'Boda',
					corporate: 'Evento Corporativo',
					festival: 'Festival/Picnic',
					private: 'Fiesta Privada',
					other: 'Otro',
				},
				stations: 'Estaciones de Interés *',
				st_pancakes: 'Mini Pancakes',
				st_icecream: 'Helado Suave',
				st_cheese: 'Tabla de Quesos',
				contact_hours: 'Horario de contacto preferido',
				contact_hours_placeholder: 'ej. 10:00-14:00 o después de las 18:00',
				message: 'Información Adicional',
				submit: 'Enviar Consulta',
				progress_text: 'Complete los datos para que podamos preparar una oferta (0%)',
				message_placeholder: 'Describa sus necesidades, preguntas o preferencias...',
				email_error: 'Dirección de correo electrónico no válida',
				stations_error: 'Seleccione al menos una estación',
				required: 'Este campo es obligatorio',
				sending: 'Enviando...',
				success_msg: 'Los detalles de la consulta han sido enviados. Confirmaremos la recepción del mensaje. Nos pondremos en contacto con usted pronto.',
				error_msg: 'Error al enviar. Verifique su conexión o inténtelo más tarde.',
			},
			cookies: {
				text: 'Este sitio utiliza cookies para garantizar la mejor calidad. Al utilizar el sitio, usted acepta su uso.',
				accept: 'Aceptar',
				reject: 'Rechazar',
			},
			footer: {
				desc: 'Estaciones de degustación móviles para eventos en toda Polonia.',
				phone: 'Teléfono: <a href="tel:+48883392688" class="phone-link">+48 883 392 688</a>',
				quick_links: 'Enlaces Rápidos',
			},
			gallery: {
				badge: 'MOMENTOS REALES',
				title: 'Descubre Raricart en eventos reales',
				subtitle: 'Así luce la estación cuando empieza la fiesta: productos frescos, preparación en vivo, combinaciones a medida y personas disfrutando al máximo.',
				see_more: 'VER MÁS FOTOGRAFÍAS',
			},
			reviews: {
				badge: 'GOOGLE REVIEWS &bull; 100% VERIFICADO',
				title: 'Lo que dicen invitados y organizadores',
				subtitle: 'Emoción genuina, platos vacíos y tranquilidad para el anfitrión. Así recuerdan Raricart parejas, empresas y celebraciones privadas.',
				trust_summary: '<strong>5.0 / 5.0</strong> &bull; Más de 120 eventos realizados &bull; 100% clientes satisfechos',
				google_verified_meta: 'Perfil de Google &bull; Más de 120 eventos realizados',
				see_google_maps: 'Ver en Google Maps',
				verified: 'Reserva verificada',
				cta_title: '¿Quieres que tus invitados recuerden tu evento así?',
				cta_desc: 'Escríbenos o llámanos. Comprobaremos la disponibilidad de la estación para tu fecha en menos de 24 horas.',
				cta_btn: 'CONSULTAR DISPONIBILIDAD',
				cta_google: 'OPINIONES EN GOOGLE MAPS'
			},
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
				},
			},
		},
	}

	let tick = false
	let currentGalleryIndex = 0
	// Current language state
	let currentLang = 'pl'

	const galleryImages = []

	// Intro Animation Timeouts (for cancelation)
	let introTimeout1, introTimeout2

	// --- Cache Elements (Optimization) ---
	const ui = {
		brand: null,
		brandText1: null,
		brandText2: null,
		nav: null,
		hamburger: null,
		bg: null,
		scroll: null,
		videoBg: null,
		onasSection: null,
		gallerySection: null,
		navBg: null, // New Background Element
	}

	function cacheElements() {
		ui.brand = document.getElementById('brand')
		ui.brandText1 = document.getElementById('brandText1')
		ui.brandText2 = document.getElementById('brandText2')
		ui.nav = document.getElementById('nav')
		ui.navBg = document.getElementById('navBg') // Cache new element
		ui.hamburger = document.getElementById('hamburger')
		ui.bg = document.getElementById('bg')
		ui.scroll = document.getElementById('scroll')
		ui.videoBg = document.getElementById('videoBg')
		ui.onasSection = document.getElementById('onas')
		ui.gallerySection = document.getElementById('realizacje-parallax')
		// NOTE: measureLayout() deferred to avoid blocking start-up
	}

	function measureLayout() {
		// ui.onasSection offset removed (unused)
		if (ui.gallerySection) {
			ui.cachedGalleryOffset = ui.gallerySection.offsetTop
			ui.cachedGalleryHeight = ui.gallerySection.offsetHeight
		}
	}

	// --- Initial Setup ---
	// --- Initial Setup ---
	document.addEventListener('DOMContentLoaded', function () {
		// Architectural Fix: Instant interactive readiness (no frozen intro)
		sessionStorage.setItem('heroIntroPlayed', '1')

		cacheElements() // Initialize cache

		// --- ARCHITECTURAL FIX: Subpage Handling ---
		const isHomePage = document.body.classList.contains('is-homepage')
		if (!isHomePage) {
			// Immediately set scrolled state for subpages
			if (ui.brand) ui.brand.classList.add('moving')
			if (ui.nav) ui.nav.classList.add('nav-scrolled', 'visible') // Both for compatibility
			if (ui.bg) ui.bg.classList.add('shrink')
			if (ui.navBg) ui.navBg.classList.add('visible')
			if (ui.hamburger) ui.hamburger.classList.add('visible')
			const header = document.getElementById('main-header')
			if (header) header.style.pointerEvents = 'auto'

			// We skip the rest of the homepage-specific initialization
		} else {
			updateLayout() // Initial layout check

			setTimeout(() => {
				measureLayout()
			}, 50)

			setTimeout(() => {
				if (ui.videoBg) {
					ui.videoBg.classList.add('visible')
					const vid = ui.videoBg.querySelector('video')
					if (vid) {
						vid.muted = true
						const playPromise = vid.play()
						if (playPromise !== undefined) {
							playPromise.catch(() => {})
						}
					}
				}
			}, 100)
		}

		// Scroll Button Logic (Skip Intro + Scroll)
		const scrollBtn = document.getElementById('scroll')
		if (scrollBtn) {
			scrollBtn.addEventListener('click', () => {
				skipIntro()
				const onas = document.getElementById('onas')
				if (onas) {
					setTimeout(() => onas.scrollIntoView({ behavior: 'smooth' }), 10)
				}
			})
		}
	})

	// --- Scroll & Layout Logic ---
	function updateLayout(scrollY, vh) {
		// ARCHITECTURAL FIX: Disable hero logic on subpages completely
		const isHomePage = document.body.classList.contains('is-homepage')
		if (!isHomePage) return

		// Optimization: Use cached elements
		// Safety check if elements exist (e.g. if script loads before DOM - though we use 'load' event)
		if (!ui.brand) cacheElements()

		// Null check for subpages without hero
		if (!ui.brand || !ui.brandText1) {
			// Subpage logic: navbar is statically visible (handled by PHP/CSS)
			// Do NOT toggle visibility here to prevent flickering.
			return
		}

		// Use passed values or fallback (fallback for direct calls outside loop)
		scrollY = scrollY !== undefined ? scrollY : window.pageYOffset
		vh = vh !== undefined ? vh : window.innerHeight

		const progress = Math.min(scrollY / vh, 1)
		const textProgress = scrollY / vh

		// --- Navbar & Logo Visibility Logic ---
		let shouldShowNavbar = false

		// User Request: Trigger when approaching "NIE TWORZYMY CATERINGU..."
		const onasHeadline = document.querySelector('#onas h2')
		if (onasHeadline) {
			const headlineRect = onasHeadline.getBoundingClientRect()
			// Trigger when the headline is getting close to the center/top of viewport
			// headlineRect.top is relative to viewport.
			// When it appears from bottom: headlineRect.top < window.innerHeight
			// User wants "when approaching", so maybe when it's about to enter or entered?
			// "zbliża do napisu" - closer to the inscription.
			// Let's trigger when the headline is within the bottom 1/3 of the screen or higher.
			if (headlineRect.top < window.innerHeight * 0.8) {
				shouldShowNavbar = true
			}
		} else {
			// Fallback
			if (scrollY >= vh - 50) {
				shouldShowNavbar = true
			}
		}

		// --- STATIC NAVBAR & LOGO (Lejek 2026 - Zagnieżdżony w pasku) ---
		if (ui.brand) ui.brand.classList.add('moving')
		if (ui.scroll) ui.scroll.classList.add('hidden')
		if (ui.brandText1) {
			ui.brandText1.classList.remove('visible')
			ui.brandText1.classList.add('hidden')
		}
		if (ui.brandText2) {
			ui.brandText2.classList.remove('visible')
			ui.brandText2.classList.add('hidden')
		}
		window._heroSequenceRunning = false
		window._heroSequencePlayed = true

		// Always show navbar & nav background, add scrolled class on scroll
		if (ui.nav) ui.nav.classList.add('visible')
		if (ui.navBg) ui.navBg.classList.add('visible')
		if (ui.hamburger) ui.hamburger.classList.add('visible')
		if (ui.bg) ui.bg.classList.add('shrink')

		if (scrollY > 30) {
			if (ui.nav) ui.nav.classList.add('nav-scrolled')
			if (ui.navBg) ui.navBg.classList.add('nav-scrolled')
		} else {
			if (ui.nav) ui.nav.classList.remove('nav-scrolled')
			if (ui.navBg) ui.navBg.classList.remove('nav-scrolled')
		}

		tick = false
	}

	// --- Actions ---
	function skipIntro() {
		// Clear pending timeouts
		clearTimeout(introTimeout1)
		clearTimeout(introTimeout2)

		// Force unlock scroll immediately
		document.body.style.overflow = ''

		// Hide texts immediately
		if (ui.brandText1) {
			ui.brandText1.classList.remove('visible')
			ui.brandText1.classList.add('hidden')
		}
		if (ui.brandText2) {
			ui.brandText2.classList.remove('visible')
			ui.brandText2.classList.add('hidden')
		}

		// Set flags as if intro finished
		window._heroSequenceRunning = false
		window._heroSequencePlayed = true
		sessionStorage.setItem('heroIntroPlayed', '1')

		// Move brand logic (optional, but good for consistency)
		if (ui.brand) ui.brand.classList.add('moving')
		if (ui.scroll) ui.scroll.classList.add('hidden')
	}

	function scrollToTop() {
		window.scrollTo({ top: 0, behavior: 'smooth' })
	}

	// --- Mobile Menu Logic (Simpler Overflow Lock) ---
	const hamburger = document.getElementById('hamburger')
	const nav = document.getElementById('nav')

	if (hamburger && nav) {
		hamburger.addEventListener('click', e => {
			e.preventDefault()
			e.stopPropagation()

			const isActive = hamburger.classList.contains('active')
			const html = document.documentElement

			if (!isActive) {
				// Opening
				hamburger.classList.add('active')
				nav.classList.add('mobile-active')
				document.body.classList.add('nav-open')

				// Lock Scroll
				document.body.style.overflow = 'hidden'
			} else {
				// Closing
				hamburger.classList.remove('active')
				nav.classList.remove('mobile-active')
				document.body.classList.remove('nav-open')

				// Unlock Scroll
				document.body.style.overflow = ''
			}
		})

		// Close on link click
		nav.querySelectorAll('a').forEach(link => {
			link.addEventListener('click', () => {
				if (hamburger.classList.contains('active')) {
					hamburger.classList.remove('active')
					nav.classList.remove('mobile-active')
					document.body.classList.remove('nav-open')
					document.body.style.overflow = ''
				}
			})
		})
	}

	function updateGalleryParallax(scrollY, vh) {
		const gallerySection = ui.gallerySection
		// Select only the columns that are explicitly marked as parallax
		const parallaxColumns = document.querySelectorAll('.gallery-column.parallax')

		if (gallerySection && parallaxColumns.length > 0) {
			// Robust calculation: Use direct viewport position
			// This works even if layout shifts occurred
			const rect = gallerySection.getBoundingClientRect()
			const windowHeight = vh || window.innerHeight

			// Check if section is in viewport (with some buffer)
			if (rect.top < windowHeight && rect.bottom > 0) {
				// Calculate progress: 0 when top enters bottom, 1 when bottom leaves top
				// But for parallax we usually want 0 at center or -1 to 1 based on viewport traversal

				// Simple shift: Move UP or DOWN based on scroll
				// Center point: When section center is at viewport center
				const sectionCenter = rect.top + rect.height / 2
				const viewportCenter = windowHeight / 2

				// dist is pixels from center
				const dist = sectionCenter - viewportCenter

				// Factor: How much to move per pixel of scroll
				const factor = 0.15

				parallaxColumns.forEach((col, index) => {
					// Apply effect to Desktop view (where column acts as a flex container)
					// On Mobile, columns have display:contents, so we must animate items instead
					const isMobile = window.innerWidth <= 768
					const direction = index % 2 === 0 ? -1 : 1
					const movement = dist * factor * direction

					if (isMobile) {
						// Parallax fallback logic for mobile (apply to children dynamically)
						const items = col.querySelectorAll('.gallery-item')
						items.forEach(item => {
							// Override the slow 0.4s intro transition to prevent lag during rapid scroll
							item.style.transition = 'transform 0.05s linear'
							item.style.transform = `translateY(${movement * 0.4}px)`
						})
					} else {
						// Desktop normal parallax
						col.style.transform = `translateY(${movement}px)`
					}
				})
			}
		}
	}

	// --- Optimized Resize Handler ---
	// Recalculate offsets ONLY on resize, not every scroll frame
	window.addEventListener(
		'resize',
		() => {
			measureLayout()
			updateLayout()
		},
		{ passive: true },
	)

	// Initial Calculation
	// measureLayout called in cacheElements

	// --- Scroll Handler (Optimized) ---
	// Use passive listener for better scroll performance
	window.addEventListener(
		'scroll',
		() => {
			if (!tick) {
				requestAnimationFrame(() => {
					const scrollY = window.pageYOffset
					const vh = window.innerHeight
					// updateBackgroundParallax(scrollY, vh); // Removed JS Parallax
					updateLayout(scrollY, vh)

					updateGalleryParallax(scrollY, vh)
					tick = false
				})
				tick = true
			}
		},
		{ passive: true },
	)

	// Initial check
	updateLayout()

	// --- Observer ---
	const sectionObserver = new IntersectionObserver(
		entries => {
			entries.forEach(entry => {
				if (entry.isIntersecting) entry.target.classList.add('visible')
			})
		},
		{ threshold: 0.1, rootMargin: '0px 0px -50px 0px' },
	)

	function toggleBodyScroll(lock) {
		if (lock) {
			// document.body.style.overflow = 'hidden'; // User requested to keep background scrollable
			document.body.classList.add('lightbox-open')
		} else {
			document.body.style.overflow = 'auto'
			document.body.classList.remove('lightbox-open')
		}
	}

	function openOfferModal(type) {
		const modal = document.getElementById('modal')
		const img = document.getElementById('modalImg')
		const title = document.getElementById('modalTitle')
		const text = document.getElementById('modalText')

		// Get data from translations based on current language
		const data = translations[currentLang]?.modals?.[type]

		if (data && modal) {
			let imgSrc = data.image // May be undefined now

			// 1. Try Specific Modal Image from Config
			if (
				window.siteContentConfig &&
				window.siteContentConfig.offer_modals &&
				window.siteContentConfig.offer_modals[type]
			) {
				imgSrc = window.siteContentConfig.offer_modals[type] + '?v=' + Date.now()
			}
			// 2. Fallback to Card Image from Config if Modal Image not set
			else if (
				window.siteContentConfig &&
				window.siteContentConfig.offer_cards &&
				window.siteContentConfig.offer_cards[type]
			) {
				imgSrc = window.siteContentConfig.offer_cards[type] + '?v=' + Date.now()
			}

			// 3. Last resort fallback (absolute path if needed, or placeholder)
			if (!imgSrc) {
				// Optional: Set a default placeholder if nothing exists
				// imgSrc = 'assets/images/placeholder.jpg';
			}

			img.src = imgSrc
			img.alt = data.title
			title.textContent = data.title
			text.innerHTML = data.content // Render HTML directly
			modal.classList.add('active')
			toggleBodyScroll(true)

			// History API: Add state
			history.pushState({ modal: 'offer' }, '', '#offer-' + type)
		}
	}

	function openGalleryModal(index) {
		currentGalleryIndex = index
		const modal = document.getElementById('galleryModal')
		const img = document.getElementById('galleryModalImg')

		if (modal && img) {
			img.src = galleryImages[index]
			// Reset styles in case they were stuck in transition
			img.style.opacity = '1'
			img.style.transform = 'scale(1)'

			modal.classList.add('active')
			toggleBodyScroll(true)

			// History API: Add state
			history.pushState({ modal: 'gallery' }, '')
		}
	}

	function closeAllModals() {
		document.querySelectorAll('.modal, .gallery-modal').forEach(m => m.classList.remove('active'))
		toggleBodyScroll(false)

		// Clean URL hash if present
		if (window.location.hash.startsWith('#offer-')) {
			history.replaceState(null, '', window.location.pathname + window.location.search)
		}
	}

	function changeGalleryImage(direction) {
		currentGalleryIndex += direction
		if (currentGalleryIndex < 0) currentGalleryIndex = galleryImages.length - 1
		if (currentGalleryIndex >= galleryImages.length) currentGalleryIndex = 0

		const img = document.getElementById('galleryModalImg')
		if (img) {
			img.style.opacity = '0'
			img.style.transform = 'scale(0.8)'
			setTimeout(() => {
				img.src = galleryImages[currentGalleryIndex]
				img.style.opacity = '1'
				img.style.transform = 'scale(1)'
			}, 200)
		}
	}

	// --- Initialization ---
	document.addEventListener('DOMContentLoaded', function () {
		// Observers
		document.querySelectorAll('.section, .section-premium').forEach(s => sectionObserver.observe(s))

		// Offer Cards
		document.querySelectorAll('.offer-card').forEach(card => {
			card.addEventListener('click', () => openOfferModal(card.getAttribute('data-offer')))
		})

		// Initial Gallery Preload from SSR Data
		const galleryDataEl = document.getElementById('galleryInitialData')
		if (galleryDataEl) {
			try {
				const preloaded = JSON.parse(galleryDataEl.textContent)
				if (Array.isArray(preloaded) && preloaded.length > 0) {
					preloaded.forEach((src, idx) => {
						galleryImages[idx] = src
					})
				}
			} catch (e) {
				console.warn('Initial gallery data parse error', e)
			}
		}

		// Gallery Items (SSR elements)
		document.querySelectorAll('.gallery-item').forEach(item => {
			const img = item.querySelector('img')
			const index = parseInt(item.getAttribute('data-index'))
			if (img && !isNaN(index) && !galleryImages[index]) {
				galleryImages[index] = img.src
			}
			item.addEventListener('click', () => openGalleryModal(index))
			item.addEventListener('keydown', e => {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault()
					openGalleryModal(index)
				}
			})
		})

		// Full Gallery Button
		const openFullBtn = document.getElementById('openFullGalleryBtn') || document.querySelector('.realizations-more-btn')
		if (openFullBtn) {
			openFullBtn.addEventListener('click', () => openGalleryModal(0))
		}

		// --- Dynamic Content Loaders ---

		// Gallery Animation Observer (Defined here to be accessible)
		const galleryObserver = new IntersectionObserver(
			entries => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						entry.target.classList.add('in-view')
						galleryObserver.unobserve(entry.target)
					}
				})
			},
			{ threshold: 0.1 },
		)

		// Initial Observation for Static Items
		document.querySelectorAll('.gallery-item').forEach(item => {
			galleryObserver.observe(item)
		})

		// Defer non-critical API calls to clear the Critical Request Chain
		const loadDynamicContent = () => {
			// 1. Availability Notice
			fetch('api/get_status.php?v=' + Date.now())
				.then(r => r.json())
				.then(data => {
					const notice = document.getElementById('availability-notice')
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
            `
						notice.style.display = 'flex'
					}
				})
				.catch(() => {
					/* Silent fail locally */
				})

			// 1.5 Dynamic Site Content (Hero, Offer, About)
			fetch('/assets/data/content.json?v=' + Date.now())
				.then(r => r.json())
				.then(content => {
					if (!content) return

					// Helper to format background and media URLs safely without prepending extra slashes to absolute URLs
					const getSafeMediaUrl = (url) => {
						if (!url || typeof url !== 'string') return ''
						const cleanUrl = url.trim()
						if (cleanUrl.startsWith('http://') || cleanUrl.startsWith('https://')) {
							return cleanUrl
						}
						return '/' + cleanUrl.replace(/^\/+/, '')
					}

					// Hero Video
					if (content.hero_video) {
						const video = document.querySelector('#videoBg video')
						if (video) {
							const targetVidUrl = getSafeMediaUrl(content.hero_video)
							const source = video.querySelector('source')
							if (source && (!source.src || !source.src.includes(targetVidUrl))) {
								source.src = targetVidUrl
								video.src = targetVidUrl
								video.load()
								video.play().catch(() => {})
							} else if (!video.src || !video.src.includes(targetVidUrl)) {
								video.src = targetVidUrl
								video.load()
								video.play().catch(() => {})
							} else if (video.paused) {
								video.play().catch(() => {})
							}
						}
					}

					// About Image
					if (content.about_image) {
						const img = document.querySelector('#onas .premium-image, #onas .premium-img')
						if (img) img.src = getSafeMediaUrl(content.about_image)
					}

					// Offer Main Image
					if (content.offer_main_image) {
						const img = document.querySelector('#oferta .premium-image, #oferta .premium-img')
						if (img) img.src = getSafeMediaUrl(content.offer_main_image)
					}

					// Offer Cards
					if (content.offer_cards) {
						window.siteContentConfig = content
						const setCardImg = (type, url) => {
							const safeUrl = getSafeMediaUrl(url)
							if (!safeUrl) return
							const cardImg = document.querySelector(`.offer-card[data-offer="${type}"] .offer-image-img`)
							if (cardImg) {
								cardImg.src = safeUrl
							}
							const cardBg = document.querySelector(`.offer-card[data-offer="${type}"] .offer-image`)
							if (cardBg) {
								cardBg.style.backgroundImage = `url('${safeUrl}')`
							}
						}
						setCardImg('pancakes', content.offer_cards.pancakes)
						setCardImg('icecream', content.offer_cards.icecream)
						setCardImg('cheese', content.offer_cards.cheese)
					}

					// Gallery Parallax Background
					if (content.gallery_bg) {
						const gallerySection = document.getElementById('realizacje-parallax')
						if (gallerySection) {
							gallerySection.style.setProperty('--bg-image', `url('${getSafeMediaUrl(content.gallery_bg)}')`)
						}
					}

					// Why Us Background
					if (content.why_us_bg) {
						const whySection = document.querySelector('.parallax-why')
						if (whySection) {
							whySection.style.setProperty('--bg-image', `url('${getSafeMediaUrl(content.why_us_bg)}')`)
						}
					}
				})
				.catch(() => {})
				.finally(() => {
					// Critical: Re-measure layout/offsets after images likely affect DOM flow or simply after data load
					setTimeout(measureLayout, 500)
				})

			const galleryGrid = document.getElementById('dynamicGalleryGrid')
			if (galleryGrid) {
				fetch('api/get_gallery.php?v=' + Date.now())
					.then(r => r.json())
					.then(images => {
						if (images.length === 0) return

						// SAFETY: Logic removed to prevent duplication of images in small galleries.
						// We now rely on dynamic column count in PHP/CSS or just render what we have.
						// const MIN_IMAGES = 15; // Removed

						// Clear static items only if we have API images
						const columns = galleryGrid.querySelectorAll('.gallery-column')
						if (columns.length === 0) return

						// Clear existing static content for replacement
						columns.forEach(col => (col.innerHTML = ''))

						// Dynamic parallax class restoration (fixes empty gallery.json fallback issues)
						if (images.length >= 5) {
							columns.forEach((col, cIdx) => {
								if (cIdx % 2 !== 0) {
									col.classList.add('parallax')
								} else {
									col.classList.remove('parallax')
								}
							})
						} else {
							columns.forEach(col => col.classList.remove('parallax'))
						}

						// Populate ALL images into lightbox array for complete fullscreen viewing
						galleryImages.length = 0
						images.forEach((src, idx) => {
							galleryImages[idx] = src
						})

						// Update count badge if present
						const countBadge = document.querySelector('.realizations-count')
						if (countBadge) {
							countBadge.textContent = `(${images.length})`
						}

						// Render top curated images in grid (max 8)
						const maxDisplay = 8
						const displayImages = images.slice(0, maxDisplay)

						displayImages.forEach((src, i) => {
							const colIndex = i % columns.length
							const col = columns[colIndex]

							const div = document.createElement('div')
							div.className = 'gallery-item in-view'
							div.setAttribute('data-index', i)

							const img = document.createElement('img')
							img.src = src
							img.loading = 'lazy'
							img.alt = 'Realizacja Raricart ' + (i + 1)
							img.style.width = '100%'
							img.style.display = 'block'

							div.appendChild(img)
							col.appendChild(div)

							// Observe for animation
							galleryObserver.observe(div)

							// Add Click Listener
							const idx = i
							div.addEventListener('click', () => openGalleryModal(idx))
						})
					})
					.catch(err => {
						// Silent fail locally - Static items remain valid
						// console.log('Running in local/offline mode regarding gallery API');
					})
			}
		} // End loadDynamicContent

		// Execute deferred loading
		if ('requestIdleCallback' in window) {
			requestIdleCallback(loadDynamicContent, { timeout: 2000 })
		} else {
			setTimeout(loadDynamicContent, 100)
		}

		// --- Global Click/Key Handlers
		document.addEventListener('click', e => {
			if (
				e.target.classList.contains('modal') ||
				e.target.classList.contains('gallery-modal') ||
				e.target.classList.contains('modal-close') ||
				e.target.classList.contains('gallery-modal-close')
			) {
				// If closing manually, go back in history to resolve the state
				if (history.state && (history.state.modal === 'offer' || history.state.modal === 'gallery')) {
					history.back()
				} else {
					closeAllModals()
				}
			}
		})

		// History API Handler (Back Button)
		window.addEventListener('popstate', e => {
			// If state is null or doesn't have our modal flag, close modals
			if (!e.state || !e.state.modal) {
				closeAllModals()
			}
		})

		document.addEventListener('keydown', e => {
			if (e.key === 'Escape') {
				if (history.state && (history.state.modal === 'offer' || history.state.modal === 'gallery')) {
					history.back()
				} else {
					closeAllModals()
				}
			}
			if (document.getElementById('galleryModal').classList.contains('active')) {
				if (e.key === 'ArrowLeft') changeGalleryImage(-1)
				if (e.key === 'ArrowRight') changeGalleryImage(1)
			}
		})

		// Gallery Buttons
		const prevBtn = document.querySelector('.gallery-prev')
		const nextBtn = document.querySelector('.gallery-next')
		if (prevBtn)
			prevBtn.addEventListener('click', e => {
				e.stopPropagation()
				changeGalleryImage(-1)
			})
		if (nextBtn)
			nextBtn.addEventListener('click', e => {
				e.stopPropagation()
				changeGalleryImage(1)
			})

		// Contact Form
		const form = document.getElementById('form')

		// Trigger "Poor Man's Cron" cleanly after page load (Zero SEO/Performance impact)
		window.addEventListener('load', () => {
			if ('requestIdleCallback' in window) {
				requestIdleCallback(() => sendPing())
			} else {
				setTimeout(() => sendPing(), 2000)
			}
		})

		function sendPing() {
			fetch('api/contact.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ action: 'ping' }),
			}).catch(() => {})
		}

		if (form) {
			const progressBar = document.getElementById('form-progress')
			const progressText = document.getElementById('progress-text')
			const fields = form.querySelectorAll('input, select, textarea')

			// Progress Bar Logic
			function updateProgress() {
				if (!progressBar || !progressText) return

				let filled = 0
				let total = 0

				// Count significant fields (ONLY REQUIRED)
				fields.forEach(f => {
					if (f.type !== 'hidden' && f.type !== 'submit' && f.type !== 'checkbox') {
						// Only count if required
						if (f.hasAttribute('required')) {
							total++
							if (f.value.trim() !== '') filled++
						}
					}
				})

				// Checkboxes count as 1 group (Manual check as they are required by logic)
				const snowflakes = form.querySelectorAll('input[name="stations"]')
				if (snowflakes.length > 0) {
					total++
					if (Array.from(snowflakes).some(cb => cb.checked)) filled++
				}

				const percent = Math.round((filled / total) * 100)
				progressBar.style.width = percent + '%'
				const progressTemplate = translations[currentLang]?.form?.progress_text || 'Uzupełnij dane (0%)'
				progressText.textContent = progressTemplate.replace('0%', `${percent}%`)
			}

			fields.forEach(f => {
				f.addEventListener('input', updateProgress)
				f.addEventListener('change', updateProgress)

				// Silent Autosave Logic
				f.addEventListener('blur', () => {
					const formDataRaw = new FormData(form)
					const data = Object.fromEntries(formDataRaw.entries())

					// Map checkboxes manually if needed, but for autosave basic info is key
					// Only save if we have at least phone or email
					if (data.phone || data.email) {
						// Collect stations for draft
						const stations = Array.from(form.querySelectorAll('input[name="stations"]:checked'))
							.map(cb => cb.nextElementSibling.textContent)
							.join(', ')

						const payload = {
							...data,
							stations: stations, // Ensure stations are sent in draft too
							is_partial: true,
						}

						fetch('api/contact.php', {
							method: 'POST',
							headers: { 'Content-Type': 'application/json' },
							body: JSON.stringify(payload),
						}).catch(() => {}) // Silent fail is fine for autosave
					}
				})
			})

			// --- ABANDONED LEAD: beforeunload + visibilitychange ---
			let formSubmitted = false
			let abandonedSent = false

			// --- Toast Notification (replaces alert()) ---
			function showToast(type, title, message) {
				// Remove existing toast
				const existing = document.querySelector('.toast-notification')
				if (existing) existing.remove()

				const toast = document.createElement('div')
				toast.className = `toast-notification toast-${type}`
				toast.innerHTML = `
					<span class="toast-icon">${type === 'success' ? '✅' : '❌'}</span>
					<div class="toast-body">
						<div class="toast-title">${title}</div>
						<div class="toast-message">${message}</div>
					</div>
					<button class="toast-close" aria-label="Zamknij">&times;</button>
				`

				document.body.appendChild(toast)

				// Close on click
				toast.querySelector('.toast-close').addEventListener('click', () => dismissToast(toast))

				// Auto-dismiss after 6s
				setTimeout(() => dismissToast(toast), 6000)
			}

			function dismissToast(toast) {
				if (!toast || !toast.parentNode) return
				toast.classList.add('toast-out')
				setTimeout(() => toast.remove(), 400)
			}

			function sendAbandonedLead() {
				if (formSubmitted || abandonedSent) return
				abandonedSent = true

				const formDataRaw = new FormData(form)
				const data = Object.fromEntries(formDataRaw.entries())

				if (!data.phone && !data.email) return // Nothing worth saving

				const stations = Array.from(form.querySelectorAll('input[name="stations"]:checked'))
					.map(cb => cb.nextElementSibling.textContent)
					.join(', ')

				const payload = JSON.stringify({
					...data,
					stations: stations,
					is_partial: true,
					is_abandoned: true,
				})

				// fetch with keepalive is more reliable than sendBeacon for application/json with CORS
				fetch('api/contact.php', {
					method: 'POST',
					keepalive: true,
					headers: { 'Content-Type': 'application/json' },
					body: payload
				}).catch(() => {})
			}

			// Fires when user closes tab / navigates away
			window.addEventListener('beforeunload', sendAbandonedLead)

			// Fires when user switches tab / minimizes browser (mobile-friendly)
			document.addEventListener('visibilitychange', () => {
				if (document.visibilityState === 'hidden') {
					sendAbandonedLead()
				}
			})

			// Phone Validation (Input Masking + Min Length)
			const phoneInput = form.querySelector('input[name="phone"]')
			if (phoneInput) {
				phoneInput.addEventListener('input', function (e) {
					// Allow only digits, spaces, and '+' at start
					let val = e.target.value
					val = val.replace(/[^\d\s+]/g, '') // Remove illegal chars

					// max length 20 (safe limit for international numbers)
					if (val.length > 20) val = val.substring(0, 20)

					e.target.value = val
				})

				// Validate min digits on blur
				phoneInput.addEventListener('blur', function () {
					const digits = this.value.replace(/\D/g, '')
					if (digits.length > 0 && digits.length < 9) {
						this.classList.add('input-error')
						if (!this.parentNode.querySelector('.phone-error')) {
							const msg = document.createElement('small')
							msg.className = 'error-message phone-error'
							msg.textContent = 'Numer musi mieć min. 9 cyfr'
							this.parentNode.appendChild(msg)
						}
					} else {
						this.classList.remove('input-error')
						const existing = this.parentNode.querySelector('.phone-error')
						if (existing) existing.remove()
					}
				})
			}

			form.addEventListener('submit', function (e) {
				e.preventDefault()
				formSubmitted = true // Block beacon BEFORE async fetch (race condition fix)

				// --- Visual Validation ---
				let isValid = true

				// Clear previous errors
				form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'))
				form.querySelectorAll('.error-message').forEach(el => el.remove())

				// Validate text/select fields
				fields.forEach(f => {
					if (f.hasAttribute('required') && !f.value.trim()) {
						isValid = false
						f.classList.add('input-error')

						const msg = document.createElement('small')
						msg.className = 'error-message'
						msg.textContent = translations[currentLang]?.form?.required || 'Required'
						f.parentNode.appendChild(msg)
					}

					// Email format validation (form has novalidate, so HTML5 check is off)
					if (f.name === 'email' && f.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.value.trim())) {
						isValid = false
						f.classList.add('input-error')

						const msg = document.createElement('small')
						msg.className = 'error-message'
						msg.textContent = translations[currentLang]?.form?.email_error || 'Nieprawidłowy adres email'
						f.parentNode.appendChild(msg)
					}

					// Message minlength (prevent single-char spam like ".")
					if (f.name === 'message' && f.value.trim().length > 0 && f.value.trim().length < 3) {
						isValid = false
						f.classList.add('input-error')

						const msg = document.createElement('small')
						msg.className = 'error-message'
						msg.textContent = 'Minimum 3 znaki'
						f.parentNode.appendChild(msg)
					}
				})

				// Validate Stations
				const checkboxes = form.querySelectorAll('input[name="stations"]')
				const stationsChecked = Array.from(checkboxes).some(cb => cb.checked)
				if (!stationsChecked) {
					isValid = false
					// Find container to highlight
					const container = form.querySelector('.checkbox-group')
					if (container) {
						const msg = document.createElement('small')
						msg.className = 'error-message'
						msg.style.marginTop = '10px'
						// Use translation key: form.stations_error
						msg.textContent = translations[currentLang]?.form?.stations_error || 'Select a station'
						container.parentNode.appendChild(msg)
					}
				}

				if (!isValid) {
					// Scroll to first error
					const firstError = form.querySelector('.input-error, .error-message')
					if (firstError) {
						firstError.scrollIntoView({ behavior: 'smooth', block: 'center' })
					}
					return // Stop submission
				}

				// --- Submission ---
				const stations = Array.from(form.querySelectorAll('input[name="stations"]:checked'))
					.map(cb => cb.nextElementSibling.textContent)
					.join(', ') // Use label text directly

				const formData = {
					website_check: form.website_check.value, // Antispam
					name: form.name.value,
					email: form.email.value,
					phone: form.phone.value,
					date: form.date.value,
					location: form.location.value,
					guests: form.guests.value,
					budget: form.budget.value,
					event_type: form.event_type.value,
					stations: stations,
					contact_hours: form.contact_hours.value,
					message: form.message.value || 'Brak dodatkowej wiadomości',
				}

				const submitBtn = form.querySelector('.cta-primary')
				const originalText = submitBtn.textContent
				// Use translation for "Sending..."
				submitBtn.textContent = translations[currentLang]?.form?.sending || 'Sending...'
				submitBtn.disabled = true

				// Send to PHP script
				fetch('api/contact.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(formData),
				})
					.then(response => response.json())
					.then(data => {
						if (data.status === 'success') {
							formSubmitted = true
							const successMsg = translations[currentLang]?.form?.success_msg || 'Message sent successfully!'
							showToast('success', translations[currentLang]?.form?.submit || 'Wysłano', successMsg)
							form.reset()
							updateProgress() // Reset progress bar
						} else {
							formSubmitted = false // Allow retry
							showToast('error', 'Błąd', data.message || 'Wystąpił błąd')
						}
					})
					.catch(err => {
						console.error('Error:', err)
						formSubmitted = false // Allow retry
						const errorMsg = translations[currentLang]?.form?.error_msg || 'Error sending.'
						showToast('error', 'Błąd wysyłki', errorMsg)
					})
					.finally(() => {
						submitBtn.textContent = translations[currentLang]?.form?.submit || originalText
						submitBtn.disabled = false
					})
			})
		}

		// Brand Scroll To Top
		const brand = document.getElementById('brand')
		if (brand) {
			brand.addEventListener('click', scrollToTop)
		}

		updateLayout()

		// Language Logic
		const langBtns = document.querySelectorAll('.lang-btn')

		function updateLanguage(lang) {
			currentLang = lang // Update global state
			langBtns.forEach(btn => btn.classList.toggle('active', btn.dataset.lang === lang))

			document.querySelectorAll('[data-i18n]').forEach(el => {
				const key = el.getAttribute('data-i18n')
				const keys = key.split('.')
				let text = translations[lang]

				if (text) {
					keys.forEach(k => {
						if (text) text = text[k]
					})
				}

				if (typeof text === 'string') {
					el.innerHTML = text
				}
			})

			// Handle Placeholders
			document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
				const key = el.getAttribute('data-i18n-placeholder')
				const keys = key.split('.')
				let text = translations[lang]

				if (text) {
					keys.forEach(k => {
						if (text) text = text[k]
					})
				}

				if (typeof text === 'string') {
					el.placeholder = text
				}
			})
		}

		langBtns.forEach(btn => {
			btn.addEventListener('click', () => updateLanguage(btn.dataset.lang))
		})
	})
})()

// --- Cookie & Analytics Logic ---
document.addEventListener('DOMContentLoaded', () => {
	function loadAnalytics() {
		// Prevent double loading
		if (document.getElementById('ga-script')) return

		const script = document.createElement('script')
		script.id = 'ga-script'
		script.async = true
		script.src = 'https://www.googletagmanager.com/gtag/js?id=G-K4ZBMDXLW7'
		document.head.appendChild(script)

		window.dataLayer = window.dataLayer || []
		function gtag() {
			dataLayer.push(arguments)
		}
		gtag('js', new Date())
		gtag('config', 'G-K4ZBMDXLW7')
	}

	const consent = localStorage.getItem('raricart_cookies_consent')

	if (consent === 'granted') {
		loadAnalytics()
	} else if (consent === 'denied') {
		// Do nothing (Analytics blocked)
	} else {
		// Migration check: If old key exists, treat as granted
		if (localStorage.getItem('raricart_cookies_accepted')) {
			localStorage.setItem('raricart_cookies_consent', 'granted')
			localStorage.removeItem('raricart_cookies_accepted') // Cleanup
			loadAnalytics()
		} else {
			// Show banner (No choice made yet)
			const banner = document.getElementById('cookie-banner')
			if (banner) {
				banner.style.display = 'block'
				setTimeout(() => (banner.style.opacity = '1'), 10)
			}
		}
	}

	// Accept Button
	const acceptBtn = document.getElementById('accept-cookies')
	if (acceptBtn) {
		acceptBtn.addEventListener('click', () => {
			localStorage.setItem('raricart_cookies_consent', 'granted')
			loadAnalytics()
			closeBanner()
		})
	}

	// Reject Button
	const rejectBtn = document.getElementById('reject-cookies')
	if (rejectBtn) {
		rejectBtn.addEventListener('click', () => {
			localStorage.setItem('raricart_cookies_consent', 'denied')
			closeBanner()
		})
	}

	function closeBanner() {
		const banner = document.getElementById('cookie-banner')
		if (banner) {
			banner.style.opacity = '0'
			setTimeout(() => (banner.style.display = 'none'), 500)
		}
	}

	// Cookie & Analytics Logic
	// ... code ...

	// --- SCROLL TO SECTION (query param ?goto= OR hash #) ---
	// ?goto= bypasses native browser hash scroll entirely (preferred for cross-page links).
	// #hash kept as fallback for in-page links.
	const gotoParam = new URLSearchParams(window.location.search).get('goto')
	const scrollTarget = gotoParam ? '#' + gotoParam : window.location.hash

	if (scrollTarget) {
		const NAVBAR_OFFSET = 100

		// 1. Force-reveal ALL sections (skip animations)
		document.querySelectorAll('.section, .section-premium').forEach(s => {
			s.style.transition = 'none'
			s.classList.add('visible')
		})
		document.body.offsetHeight // Force reflow

		// 2. Scroll helper
		function scrollToTarget() {
			const el = document.querySelector(scrollTarget)
			if (el) {
				const y = el.getBoundingClientRect().top + window.pageYOffset - NAVBAR_OFFSET
				window.scrollTo({ top: y, behavior: 'instant' })
			}
		}

		// 3. Scroll immediately
		scrollToTarget()

		// 4. Re-scroll on full load (images/fonts shift layout)
		window.addEventListener('load', () => {
			scrollToTarget()
			// Clean URL: remove ?goto= param (leave clean URL in address bar)
			if (gotoParam) {
				const cleanUrl = window.location.pathname + '#' + gotoParam
				history.replaceState(null, '', cleanUrl)
			}
			if ('scrollRestoration' in history) history.scrollRestoration = 'auto'
		})

		// 5. Re-enable transitions for future scroll reveals
		requestAnimationFrame(() => {
			requestAnimationFrame(() => {
				document.querySelectorAll('.section, .section-premium').forEach(s => {
					s.style.transition = ''
				})
			})
		})
	}

	// 6. Floating CTA Button Visibility Handler
	const floatingCta = document.getElementById('floatingCta');
	if (floatingCta) {
		const footer = document.querySelector('footer');
		window.addEventListener('scroll', () => {
			const y = window.scrollY;
			if (y > 300) {
				floatingCta.classList.add('visible');
			} else {
				floatingCta.classList.remove('visible');
			}
			if (footer) {
				const footerTop = footer.getBoundingClientRect().top;
				if (footerTop < window.innerHeight) {
					floatingCta.classList.remove('visible');
				}
			}
		}, { passive: true });
	}
})
