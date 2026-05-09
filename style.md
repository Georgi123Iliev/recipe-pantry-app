<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рецептите на Мама | Начало</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Caveat:wght@400;600;700&family=Lora:ital,wght@0,400;0,600;1,400&family=Montserrat:wght@400;500;600&display=swap');

        :root {
            --bg-beige: #F7ECD0; /* Oat Beige */
            --paper-white: #FFFDF8;
            --text-dark: #4E342E; /* Cocoa Roast */
            --text-muted: #8D6E63;
            --cherry-red: #C24641; 
            --butter-yellow: #FFFD74;
            --tape-color: rgba(255, 255, 230, 0.7);
        }

        body {
            margin: 0;
            padding: 2rem 5%;
            font-family: 'Lora', serif;
            color: var(--text-dark);
            /* Cozy Gingham Tablecloth Pattern */
            background-color: var(--bg-beige);
            background-image: 
                linear-gradient(90deg, rgba(194, 70, 65, 0.05) 50%, transparent 50%),
                linear-gradient(rgba(194, 70, 65, 0.05) 50%, transparent 50%);
            background-size: 40px 40px;
        }

        /* Handwritten elements */
        .handwritten {
            font-family: 'Caveat', cursive;
        }

        /* Top Navigation */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background-color: var(--paper-white);
            padding: 1rem 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-radius: 8px;
            /* Subtle paper texture */
            background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"><filter id="noiseFilter"><feTurbulence type="fractalNoise" baseFrequency="0.65" numOctaves="3" stitchTiles="stitch"/></filter><rect width="100%" height="100%" filter="url(%23noiseFilter)" opacity="0.05"/></svg>');
        }

        .logo {
            font-size: 2.5rem;
            color: var(--cherry-red);
            margin: 0;
            transform: rotate(-2deg);
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--cherry-red);
        }

        .btn-add {
            background-color: var(--text-dark);
            color: var(--paper-white);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 4px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 2px 2px 0px var(--cherry-red); /* Retro print shadow */
            transition: transform 0.1s;
        }

        .btn-add:active {
            transform: translate(2px, 2px);
            box-shadow: 0px 0px 0px var(--cherry-red);
        }

        /* Main Notebook Layout */
        .journal-container {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
            align-items: start;
        }

        /* Lined Paper Pattern */
        .notebook-page {
            background-color: var(--paper-white);
            border-radius: 4px;
            padding: 3rem 4rem 4rem 5rem;
            box-shadow: -5px 5px 15px rgba(78, 52, 46, 0.15);
            position: relative;
            /* Lined paper lines */
            background-image: repeating-linear-gradient(transparent, transparent 31px, #e1daca 31px, #e1daca 32px);
            line-height: 32px; /* Matches the line height */
        }

        /* The Red Margin Line */
        .notebook-page::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50px;
            width: 2px;
            border-left: 3px double var(--cherry-red);
            opacity: 0.5;
        }

        .notebook-page h1 {
            font-family: 'Lora', serif;
            font-size: 2.2rem;
            margin-top: 0;
            line-height: 1.2;
            color: var(--text-dark);
            background-color: var(--paper-white); /* Hides lines behind title */
            display: inline-block;
        }

        /* Polaroid Style Image */
        .polaroid {
            background-color: #fff;
            padding: 15px 15px 45px 15px;
            box-shadow: 3px 4px 10px rgba(0,0,0,0.15);
            transform: rotate(2deg);
            width: 80%;
            margin: 2rem auto;
            position: relative;
        }

        .polaroid-img {
            background-color: var(--text-muted);
            height: 250px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--paper-white);
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
        }

        .polaroid-caption {
            position: absolute;
            bottom: 10px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 1.5rem;
            color: var(--text-dark);
        }

        /* Tape effect */
        .tape {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%) rotate(-3deg);
            width: 100px;
            height: 30px;
            background-color: var(--tape-color);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            opacity: 0.8;
            backdrop-filter: blur(2px);
        }

        /* Right Sidebar - Pantry & Quick Links */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* Recipe Box Card (Моят Килер) */
        .recipe-box {
            background-color: #fdfaf0;
            border: 2px solid var(--text-muted);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: inset 0 0 20px rgba(141, 110, 99, 0.1), 4px 4px 0px var(--text-muted);
        }

        .recipe-box h3 {
            font-family: 'Montserrat', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1.1rem;
            margin-top: 0;
            border-bottom: 2px dashed var(--text-muted);
            padding-bottom: 0.5rem;
        }

        .search-input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--text-muted);
            background-color: var(--paper-white);
            font-family: 'Caveat', cursive;
            font-size: 1.3rem;
            color: var(--text-dark);
            box-sizing: border-box;
            margin-bottom: 1rem;
            outline: none;
        }

        .search-input::placeholder {
            color: #bcaaa4;
        }

        /* Sticky Note List */
        .sticky-note {
            background-color: var(--butter-yellow);
            padding: 2rem;
            box-shadow: 2px 4px 8px rgba(0,0,0,0.1);
            transform: rotate(-1deg);
            position: relative;
        }

        .sticky-note ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sticky-note li {
            font-family: 'Caveat', cursive;
            font-size: 1.6rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            line-height: 1.5;
            padding-left: 20px;
            position: relative;
        }

        .sticky-note li::before {
            content: '-';
            position: absolute;
            left: 0;
            color: var(--cherry-red);
        }

        .pin {
            position: absolute;
            top: 10px;
            left: 50%;
            width: 12px;
            height: 12px;
            background-color: var(--cherry-red);
            border-radius: 50%;
            box-shadow: 1px 2px 4px rgba(0,0,0,0.3), inset -2px -2px 4px rgba(0,0,0,0.3);
            transform: translateX(-50%);
        }

        /* Quick Recipe List */
        .latest-recipes {
            margin-top: 2rem;
        }

        .recipe-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e1daca;
        }

        .recipe-row:hover {
            background-color: rgba(247, 236, 208, 0.3);
            cursor: pointer;
        }

        .recipe-row span.time {
            font-family: 'Caveat', cursive;
            color: var(--cherry-red);
            font-size: 1.3rem;
        }

        @media (max-width: 900px) {
            .journal-container {
                grid-template-columns: 1fr;
            }
            .notebook-page {
                padding: 2rem 2rem 2rem 3.5rem;
            }
            .notebook-page::before {
                left: 30px;
            }
        }
    </style>
</head>
<body>

    <nav>
        <h1 class="logo handwritten">Рецептите на Мама</h1>
        <div class="nav-links">
            <a href="#" style="color: var(--cherry-red);">Начало</a>
            <a href="#">Каталог</a>
            <a href="#">Моят Килер</a>
        </div>
        <button class="btn-add">+ Нова Рецепта</button>
    </nav>

    <div class="journal-container">
        
        <div class="notebook-page">
            <div style="font-family: 'Caveat', cursive; font-size: 1.5rem; color: var(--text-muted); float: right; margin-top: -15px;">24 Май, Събота</div>
            
            <h1>Добре дошли в кухнята!</h1>
            <p style="font-size: 1.1rem; text-align: justify;">
                Тук пазим вкусовете, които ни връщат у дома. Някои рецепти са изрязани от стари вестници, други са надраскани набързо върху салфетка, но всички те носят спомена за споделени вечери и ухание на топъл хляб.
            </p>

            <div class="polaroid">
                <div class="tape"></div>
                <div class="polaroid-img">
                    [Снимка: Семейството около масата]
                </div>
                <div class="polaroid-caption handwritten">Обядът при баба, лятото на '98</div>
            </div>

            <h2 style="font-family: 'Lora', serif; font-style: italic; margin-top: 3rem; background: var(--paper-white); display: inline-block;">Разлисти тетрадката:</h2>
            
            <div class="latest-recipes">
                <div class="recipe-row">
                    <span class="time">45 мин</span>
                    <span style="font-weight: 600;">Домашна лютеница (по рецепта на леля)</span>
                </div>
                <div class="recipe-row">
                    <span class="time">120 мин</span>
                    <span style="font-weight: 600;">Празнична погача за прощъпулник</span>
                </div>
                <div class="recipe-row">
                    <span class="time">20 мин</span>
                    <span style="font-weight: 600;">Мекиците на дядо за неделна сутрин</span>
                </div>
            </div>
        </div>

        <div class="sidebar">
            
            <div class="recipe-box">
                <h3>Какво имаш в хладилника?</h3>
                <p style="font-size: 0.9rem; font-style: italic; color: var(--text-muted);">Въведи продуктите, а аз ще ти кажа какво да сготвим.</p>
                
                <input type="text" class="search-input" placeholder="напр. яйца, брашно, сирене...">
                
                <button class="btn-add" style="width: 100%; text-align: center; background-color: var(--cherry-red); color: white;">Търси рецепта</button>
            </div>

            <div class="sticky-note">
                <div class="pin"></div>
                <h3 style="font-family: 'Caveat', cursive; font-size: 2rem; margin: 0 0 10px 0; color: var(--text-dark); text-align: center;">За пазаруване:</h3>
                <ul>
                    <li>Прясно мляко (3%)</li>
                    <li>Кубче жива мая</li>
                    <li>Домати за соса</li>
                    <li style="text-decoration: line-through; color: var(--text-muted);">Кора яйца</li>
                </ul>
            </div>

        </div>

    </div>

</body>
</html>