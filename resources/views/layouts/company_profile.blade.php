<!DOCTYPE html>
<html lang="id">

<head>
    <title>Jasa NDT Batam | Geotama Global Intijaya</title>

    <meta name="description"
        content="Geotama menyediakan jasa Non Destructive Testing (NDT) di Batam seperti MT, PT, UT untuk industri kapal dan konstruksi. Profesional dan bersertifikat.">

    <meta name="keywords"
        content="NDT Batam, jasa NDT Batam, ultrasonic testing Batam, penetrant test Batam, magnetic test Batam">

    <meta name="author" content="Geotama Global Intijaya">
    <meta name="robots" content="index, follow">

    <link rel="canonical" href="{{ url('/') }}">

    <!-- Open Graph -->
    <meta property="og:title" content="Jasa NDT Batam - Geotama">
    <meta property="og:description"
        content="Layanan inspeksi NDT profesional di Batam untuk industri kapal dan konstruksi.">
    <meta property="og:image" content="{{ asset('template/assets/images/dokumentasi/foto_utama.JPG') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('template/assets/images/icon-geotama.ico') }}" type="image/x-icon" />


    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --navy: #0D2B55;
            --navy-mid: #1A3F73;
            --navy-light: #1E4D8C;
            --blue-accent: #2E7BCC;
            --blue-bright: #3A8FE8;
            --white: #FFFFFF;
            --off-white: #F5F7FA;
            --gray-light: #E8ECF2;
            --gray-mid: #8A96A8;
            --gray-dark: #3D4A5C;
            --text-dark: #1A232E;
            --text-body: #4A5568;
            --border: #D1DAE8;
            --font: 'Barlow', sans-serif;
            --font-cond: 'Barlow Condensed', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font);
            color: var(--text-dark);
            background: var(--white);
            font-size: 16px;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ─── NAVBAR ─── */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(13, 43, 85, 0.97);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5%;
            height: 68px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .nav-logo-icon {
            width: 38px;
            height: 38px;
            background: black;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-cond);
            font-weight: 700;
            font-size: 18px;
            color: var(--white);
            letter-spacing: 1px;
        }

        .nav-logo-text {
            font-family: var(--font-cond);
            font-size: 22px;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 1px;
        }

        .nav-logo-text span {
            color: var(--blue-bright);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.4px;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--white);
        }


        .nav-login {
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: var(--white) !important;
            padding: 8px 18px;
            border-radius: 5px;
            font-weight: 600 !important;
            transition: background 0.2s, border-color 0.2s !important;
        }

        .nav-login:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.6);
        }

        .nav-cta {
            background: var(--blue-accent);
            color: var(--white) !important;
            padding: 9px 20px;
            border-radius: 5px;
            font-weight: 600 !important;
            transition: background 0.2s !important;
        }

        .nav-cta:hover {
            background: var(--blue-bright) !important;
            color: var(--white) !important;
        }

        /* ─── HERO ─── */
        .hero {
            min-height: 100vh;
            background: var(--navy);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 120px 5% 80px;
        }

        .hero-bg-pattern {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(46, 123, 204, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(46, 123, 204, 0.06) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .hero-bg-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(46, 123, 204, 0.15) 0%, transparent 70%);
            right: -100px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 680px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(46, 123, 204, 0.2);
            border: 1px solid rgba(46, 123, 204, 0.4);
            color: #7EB8F5;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 7px 16px;
            border-radius: 20px;
            margin-bottom: 28px;
        }

        .hero-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #7EB8F5;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .hero h1 {
            font-family: var(--font-cond);
            font-size: clamp(42px, 6vw, 72px);
            font-weight: 700;
            color: var(--white);
            line-height: 1.05;
            letter-spacing: 0.5px;
            margin-bottom: 24px;
        }

        .hero h1 span {
            color: var(--blue-bright);
        }

        .hero-desc {
            font-size: 17px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.7;
            margin-bottom: 40px;
            max-width: 540px;
            font-weight: 300;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 60px;
        }

        .btn-primary {
            background: var(--blue-accent);
            color: var(--white);
            padding: 14px 32px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            letter-spacing: 0.3px;
        }

        .btn-primary:hover {
            background: var(--blue-bright);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: transparent;
            color: var(--white);
            padding: 14px 32px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 15px;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.35);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }

        .btn-secondary:hover {
            border-color: rgba(255, 255, 255, 0.7);
            background: rgba(255, 255, 255, 0.06);
        }

        .hero-stats {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding-top: 36px;
        }

        .hero-stat-num {
            font-family: var(--font-cond);
            font-size: 38px;
            font-weight: 700;
            color: var(--white);
            line-height: 1;
            margin-bottom: 4px;
        }

        .hero-stat-num span {
            color: var(--blue-bright);
        }

        .hero-stat-label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-weight: 500;
        }

        /* ─── SECTION BASE ─── */
        section {
            padding: 90px 5%;
        }

        .section-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--blue-accent);
            margin-bottom: 12px;
        }

        .section-title {
            font-family: var(--font-cond);
            font-size: clamp(32px, 4vw, 46px);
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.1;
            margin-bottom: 16px;
        }

        .section-subtitle {
            font-size: 16px;
            color: var(--text-body);
            line-height: 1.7;
            max-width: 540px;
            font-weight: 300;
        }

        .section-header {
            margin-bottom: 56px;
        }

        /* ─── ABOUT ─── */
        .about {
            background: var(--off-white);
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .about-image-placeholder {
            background: var(--gray-light);
            border-radius: 10px;
            height: 380px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px dashed var(--border);
            color: var(--gray-mid);
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
        }

        .about-image-placeholder svg {
            opacity: 0.4;
        }

        .about-text .section-subtitle {
            max-width: 100%;
            margin-bottom: 28px;
        }

        .about-highlights {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 32px;
        }

        .about-highlight-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 14px;
            color: var(--text-body);
        }

        .about-highlight-icon {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            background: rgba(46, 123, 204, 0.12);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1px;
        }

        .about-highlight-icon svg {
            width: 12px;
            height: 12px;
            fill: var(--blue-accent);
        }

        /* ─── SERVICES ─── */
        .services {
            background: var(--white);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .service-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 36px 28px;
            transition: border-color 0.25s, box-shadow 0.25s, transform 0.2s;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--blue-accent);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s;
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        .service-card:hover {
            border-color: #B3CDE8;
            box-shadow: 0 8px 32px rgba(13, 43, 85, 0.08);
            transform: translateY(-3px);
        }

        .service-icon {
            width: 52px;
            height: 52px;
            background: rgba(46, 123, 204, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 22px;
        }

        .service-icon svg {
            width: 26px;
            height: 26px;
            fill: var(--blue-accent);
        }

        .service-abbr {
            font-family: var(--font-cond);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--blue-accent);
            margin-bottom: 8px;
        }

        .service-name {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 14px;
        }

        .service-desc {
            font-size: 14px;
            color: var(--text-body);
            line-height: 1.7;
            font-weight: 300;
        }

        .service-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 24px;
            font-size: 13px;
            font-weight: 600;
            color: var(--blue-accent);
            text-decoration: none;
            letter-spacing: 0.3px;
        }

        .service-link svg {
            width: 14px;
            height: 14px;
            transition: transform 0.2s;
        }

        .service-link:hover svg {
            transform: translateX(3px);
        }

        /* ─── WHY CHOOSE ─── */
        .why {
            background: var(--navy);
            padding: 90px 5%;
        }

        .why .section-title {
            color: var(--white);
        }

        .why .section-label {
            color: #7EB8F5;
        }

        .why .section-subtitle {
            color: rgba(255, 255, 255, 0.6);
        }

        .why-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-top: 56px;
        }

        .why-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 10px;
            padding: 28px 22px;
            transition: background 0.25s;
        }

        .why-card:hover {
            background: rgba(255, 255, 255, 0.07);
        }

        .why-num {
            font-family: var(--font-cond);
            font-size: 40px;
            font-weight: 700;
            color: var(--blue-bright);
            opacity: 0.3;
            margin-bottom: 16px;
            line-height: 1;
        }

        .why-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 10px;
        }

        .why-text {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.65;
            font-weight: 300;
        }

        /* ─── GALLERY ─── */
        .gallery-section {
            background: var(--off-white);
            padding: 72px 5%;
        }

        .section-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--blue-accent);
            margin-bottom: 10px;
            font-family: var(--font);
        }

        .section-title {
            font-family: var(--font-cond);
            font-size: 38px;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.1;
            margin-bottom: 10px;
        }

        .section-subtitle {
            font-size: 15px;
            color: var(--text-body);
            font-weight: 300;
            line-height: 1.7;
            max-width: 500px;
            margin-bottom: 36px;
            font-family: var(--font);
        }

        .gallery-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .filter-btn {
            font-family: var(--font);
            font-size: 13px;
            font-weight: 600;
            padding: 7px 16px;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: white;
            color: var(--gray-mid);
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            border-color: var(--blue-accent);
            color: var(--blue-accent);
        }

        .filter-btn.active {
            background: var(--navy);
            border-color: var(--navy);
            color: white;
        }

        .masonry-grid {
            columns: 3;
            column-gap: 12px;
        }

        .masonry-item {
            break-inside: avoid;
            margin-bottom: 12px;
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            background: var(--gray-light);
            border: 1px solid var(--border);
            display: block;
        }

        .masonry-item img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 8px;
        }

        .masonry-item .placeholder {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--gray-mid);
            font-family: var(--font);
            font-size: 12px;
            font-weight: 500;
            padding: 40px 16px;
            box-sizing: border-box;
            text-align: center;
        }

        .masonry-item .placeholder svg {
            opacity: 0.35;
        }

        .masonry-item .item-tag {
            position: absolute;
            top: 8px;
            left: 8px;
            background: var(--navy);
            color: white;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            font-family: var(--font);
        }

        .masonry-item .item-overlay {
            position: absolute;
            inset: 0;
            background: rgba(13, 43, 85, 0);
            transition: background 0.25s;
            border-radius: 8px;
            display: flex;
            align-items: flex-end;
            padding: 14px;
        }

        .masonry-item:hover .item-overlay {
            background: rgba(13, 43, 85, 0.35);
        }

        .masonry-item .overlay-label {
            font-family: var(--font);
            font-size: 12px;
            font-weight: 600;
            color: white;
            opacity: 0;
            transition: opacity 0.25s;
            letter-spacing: 0.3px;
        }

        .masonry-item:hover .overlay-label {
            opacity: 1;
        }

        .gallery-note {
            margin-top: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--gray-mid);
            font-style: italic;
            font-family: var(--font);
        }

        .gallery-note svg {
            opacity: 0.5;
            flex-shrink: 0;
        }

        /* ─── CLIENTS ─── */
        .clients {
            background: var(--white);
        }

        .clients-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 48px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .clients-count {
            font-family: var(--font-cond);
            font-size: 56px;
            font-weight: 700;
            color: var(--blue-accent);
            line-height: 1;
        }

        .clients-count-label {
            font-size: 13px;
            color: var(--gray-mid);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        .clients-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
        }

        .client-logo-placeholder {
            background: var(--off-white);
            border: 1px solid var(--border);
            border-radius: 8px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-mid);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: border-color 0.2s, background 0.2s;
        }

        .client-logo-placeholder:hover {
            border-color: var(--blue-accent);
            background: rgba(46, 123, 204, 0.04);
        }

        .clients-note {
            margin-top: 32px;
            text-align: center;
            font-size: 13px;
            color: var(--gray-mid);
            font-style: italic;
        }

        /* ─── CONTACT ─── */
        .contact {
            background: var(--navy);
        }

        .contact .section-title {
            color: var(--white);
        }

        .contact .section-label {
            color: #7EB8F5;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: start;
        }

        .contact-info {}

        .contact-desc {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 300;
            line-height: 1.7;
            margin-bottom: 40px;
        }

        .contact-item {
            display: flex;
            gap: 16px;
            margin-bottom: 28px;
            align-items: flex-start;
        }

        .contact-icon {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            background: rgba(46, 123, 204, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .contact-icon svg {
            width: 18px;
            height: 18px;
            fill: var(--blue-bright);
        }

        .contact-item-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.35);
            margin-bottom: 4px;
        }

        .contact-item-val {
            font-size: 15px;
            color: var(--white);
            font-weight: 400;
        }

        .contact-form-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 36px;
        }

        .form-row {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 6px;
            color: var(--white);
            font-family: var(--font);
            font-size: 15px;
            padding: 12px 16px;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        .form-input:focus {
            border-color: rgba(46, 123, 204, 0.7);
        }

        textarea.form-input {
            resize: vertical;
            min-height: 100px;
        }

        /* ─── FOOTER ─── */
        footer {
            background: #081B36;
            padding: 32px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .footer-logo-icon {
            width: 30px;
            height: 30px;
            background: black;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-cond);
            font-weight: 700;
            font-size: 14px;
            color: var(--white);
        }

        .footer-brand-name {
            font-family: var(--font-cond);
            font-size: 18px;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 1px;
        }

        .footer-copy {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.35);
        }

        .footer-links {
            display: flex;
            gap: 24px;
            list-style: none;
        }

        .footer-links a {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--white);
        }

        @media (max-width: 900px) {

            .about-grid,
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .why-grid {
                grid-template-columns: 1fr 1fr;
            }

            .clients-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .nav-links {
                display: none;
            }

            .gallery-grid {
                grid-template-columns: 1fr 1fr;
            }

            .gallery-item:first-child {
                grid-column: 1 / 3;
            }
        }
    </style>

    {{-- <script type="application/ld+json">
    @json([
      "@context" => "https://schema.org",
      "@type" => "Organization",
      "name" => "Geotama Global Intijaya",
      "url" => url('/'),
      "logo" => asset('template/assets/images/logo/logo-geotama-removebg-preview.png'),
      "address" => [
        "@type" => "PostalAddress",
        "addressLocality" => "Batam",
        "addressRegion" => "Kepulauan Riau",
        "addressCountry" => "Indonesia"
      ]
    ])
    </script> --}}


</head>

<body>

    <!-- NAVBAR -->
    <nav>
        <a href="#" class="nav-logo">
            <div class="nav-logo-icon"><img style="width: 30px; height: auto;"
                    src="{{ asset('/template/assets/images/logo/logo-geotama-removebg-preview.png') }}"
                    alt="Geotama Logo" loading="lazy"></div>
            <span class="nav-logo-text">GEO<span>TAMA</span></span>
        </a>
        <ul class="nav-links">
            <li><a href="#about">Tentang Kami</a></li>
            <li><a href="#services">Layanan</a></li>
            <li><a href="#gallery">Proyek</a></li>
            <li><a href="#clients">Klien</a></li>
            <li><a href="#contact" class="nav-cta">Hubungi Kami</a></li>
            <li>
                @auth
                    <a href="{{ route('dashboard') }}" class="nav-login">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="nav-login">Login</a>
                @endauth
            </li>
        </ul>
    </nav>

    <!-- HERO -->
    <section class="hero" id="home">
        <div class="hero-bg-pattern"></div>
        <div class="hero-bg-glow"></div>
        <div class="hero-content">
            <div class="hero-badge">Non-Destructive Testing Specialist</div>
            <h1>Jasa NDT Batam Profesional<br><span>Inspeksi Presisi & Hasil Terpercaya</span></h1>
            <p class="hero-desc">
                Geotama menyediakan layanan Non-Destructive Testing (NDT) profesional untuk industri galangan kapal dan
                konstruksi di Batam. Pengalaman sejak 2022 dengan standar nasional.
            </p>
            <div class="hero-buttons">
                <a href="#contact" class="btn-primary">Konsultasi Gratis</a>
                <a href="#services" class="btn-secondary">Lihat Layanan</a>
            </div>
            <div class="hero-stats">
                <div>
                    <div class="hero-stat-num">68<span>+</span></div>
                    <div class="hero-stat-label">Total Klien</div>
                </div>
                <div>
                    <div class="hero-stat-num">4<span>+</span></div>
                    <div class="hero-stat-label">Tahun Pengalaman</div>
                </div>
                <div>
                    <div class="hero-stat-num">3</div>
                    <div class="hero-stat-label">Metode NDT</div>
                </div>
                <div>
                    <div class="hero-stat-num">100<span>%</span></div>
                    <div class="hero-stat-label">Standar Keamanan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT -->
    <section class="about" id="about">
        <div class="about-grid">
            <div class="about-image-placeholder">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <circle cx="8.5" cy="8.5" r="1.5" />
                    <path d="M21 15l-5-5L5 21" />
                </svg>
                <span>Upload foto kantor / tim</span>
            </div>
            <div class="about-text">
                <p class="section-label">Tentang Kami</p>
                <h2 class="section-title">Berdedikasi untuk Keselamatan & Kualitas Industri</h2>
                <p class="section-subtitle">
                    Geotama didirikan pada tahun 2022 di Batam sebagai perusahaan spesialis Non-Destructive Testing
                    (NDT). Kami melayani industri galangan kapal dan konstruksi dengan komitmen penuh terhadap akurasi,
                    keselamatan, dan standar nasional.
                </p>
                <p class="section-subtitle" style="margin-top: 16px;">
                    Dengan tim yang berpengalaman dan bersertifikat, kami memastikan setiap inspeksi dilakukan secara
                    presisi tanpa merusak material yang diuji. menjaga integritas struktur dan keamanan aset klien
                    kami.
                </p>
                <div class="about-highlights">
                    <div class="about-highlight-item">
                        <div class="about-highlight-icon">
                            <svg viewBox="0 0 12 12">
                                <path d="M10 3L5 8.5 2 5.5" />
                            </svg>
                        </div>
                        Bersertifikat Nasional
                    </div>
                    <div class="about-highlight-item">
                        <div class="about-highlight-icon">
                            <svg viewBox="0 0 12 12">
                                <path d="M10 3L5 8.5 2 5.5" />
                            </svg>
                        </div>
                        Berbasis di Batam
                    </div>
                    <div class="about-highlight-item">
                        <div class="about-highlight-icon">
                            <svg viewBox="0 0 12 12">
                                <path d="M10 3L5 8.5 2 5.5" />
                            </svg>
                        </div>
                        68+ Klien Terlayani
                    </div>
                    <div class="about-highlight-item">
                        <div class="about-highlight-icon">
                            <svg viewBox="0 0 12 12">
                                <path d="M10 3L5 8.5 2 5.5" />
                            </svg>
                        </div>
                        Berdiri Sejak 2022
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES -->
    <section class="services" id="services">
        <div class="section-header">
            <p class="section-label">Layanan Kami</p>
            <h2 class="section-title">Metode NDT yang Kami Tawarkan</h2>
            <p class="section-subtitle">Tiga metode utama pengujian non-destruktif yang kami kuasai untuk memastikan
                integritas material dan struktur Anda.</p>
        </div>
        <div class="services-grid">

            <div class="service-card">
                <div class="service-icon">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" />
                    </svg>
                </div>
                <div class="service-abbr">MT</div>
                <div class="service-name">Magnetic Particle Testing</div>
                <p class="service-desc">Metode pengujian menggunakan medan magnet untuk mendeteksi diskontinuitas
                    permukaan dan sub-permukaan pada material ferromagnetik. Ideal untuk komponen las, shaft, dan
                    struktur baja.</p>
                <a href="#contact" class="service-link">
                    Pelajari lebih lanjut
                    <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 7h10M8 3l4 4-4 4" />
                    </svg>
                </a>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14l-5-5 1.41-1.41L12 14.17l7.59-7.59L21 8l-9 9z" />
                    </svg>
                </div>
                <div class="service-abbr">PT</div>
                <div class="service-name">Penetrant Testing</div>
                <p class="service-desc">Pengujian menggunakan cairan penetrant untuk mengungkap cacat permukaan yang
                    terbuka pada berbagai material, termasuk logam, keramik, dan plastik. Efektif untuk deteksi retak
                    dan porositas.</p>
                <a href="#contact" class="service-link">
                    Pelajari lebih lanjut
                    <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 7h10M8 3l4 4-4 4" />
                    </svg>
                </a>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" />
                    </svg>
                </div>
                <div class="service-abbr">UT</div>
                <div class="service-name">Ultrasonic Testing</div>
                <p class="service-desc">Teknologi gelombang suara frekuensi tinggi untuk mengukur ketebalan material
                    dan mendeteksi cacat internal. Sangat akurat untuk inspeksi lambung kapal, pipa, dan komponen
                    struktur kritikal.</p>
                <a href="#contact" class="service-link">
                    Pelajari lebih lanjut
                    <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 7h10M8 3l4 4-4 4" />
                    </svg>
                </a>
            </div>

        </div>
    </section>

    <!-- WHY CHOOSE -->
    <section class="why" id="why">
        <div class="section-header">
            <p class="section-label">Keunggulan Kami</p>
            <h2 class="section-title">Mengapa Memilih Geotama?</h2>
            <p class="section-subtitle">Kami tidak sekadar melakukan pengujian — kami memastikan keselamatan dan
                keandalan aset Anda jangka panjang.</p>
        </div>
        <div class="why-grid">
            <div class="why-card">
                <div class="why-num">01</div>
                <div class="why-title">Tenaga Ahli Bersertifikat</div>
                <p class="why-text">Tim kami memiliki sertifikasi NDT Level II nasional dengan pengalaman lapangan di
                    industri galangan dan konstruksi.</p>
            </div>
            <div class="why-card">
                <div class="why-num">02</div>
                <div class="why-title">Hasil Akurat & Terdokumentasi</div>
                <p class="why-text">Setiap pengujian menghasilkan laporan lengkap dan terstandarisasi yang dapat
                    dijadikan dasar pengambilan keputusan teknis.</p>
            </div>
            <div class="why-card">
                <div class="why-num">03</div>
                <div class="why-title">Respon Cepat</div>
                <p class="why-text">Berbasis di Batam, kami siap mobilisasi cepat ke lokasi galangan dan proyek
                    konstruksi di seluruh Kepulauan Riau.</p>
            </div>
            <div class="why-card">
                <div class="why-num">04</div>
                <div class="why-title">Pengalaman Sejak 2022</div>
                <p class="why-text">Lebih dari satu dekade melayani industri maritim dan konstruksi menjadikan kami
                    mitra inspeksi yang terpercaya.</p>
            </div>
        </div>
    </section>

    <section id="gallery" class="gallery-section">
        <p class="section-label">Galeri Kerja</p>
        <h2 class="section-title">Dokumentasi Proyek Kami</h2>
        <p class="section-subtitle">Foto-foto kegiatan inspeksi dan pengujian NDT di lapangan oleh tim Geotama.</p>

        {{-- <div class="gallery-filter">
            <button class="filter-btn active">Semua</button>
            <button class="filter-btn">MT</button>
            <button class="filter-btn">PT</button>
            <button class="filter-btn">UT</button>
        </div> --}}

        <div class="masonry-grid">

            <div class="masonry-item">
                <img src="{{ asset('/template/assets/images/dokumentasi/foto_utama.JPG') }}"
                    alt="Tim Geotama di lapangan"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" loading="lazy">
                <div class="placeholder" style="display:none">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="M21 15l-5-5L5 21" />
                    </svg>
                    Foto tim lapangan
                </div>
                <div class="item-overlay"><span class="overlay-label">Tim Lapangan</span></div>
            </div>

            <div class="masonry-item">

                <span class="item-tag">MT</span>
                <img src="{{ asset('/template/assets/images/dokumentasi/foto_mt.JPG') }}"
                    alt="Tim Geotama di lapangan"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" loading="lazy">
                <div class="placeholder" style="display:none">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="M21 15l-5-5L5 21" />
                    </svg>
                    Inspeksi MT
                </div>
                <div class="item-overlay"><span class="overlay-label">Magnetic Particle Testing</span></div>
            </div>

            <div class="masonry-item">
                <span class="item-tag">PT</span>
                <div class="placeholder" style="padding:48px 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="M21 15l-5-5L5 21" />
                    </svg>
                    Inspeksi PT
                </div>
                <div class="item-overlay"><span class="overlay-label">Penetrant Testing</span></div>
            </div>

            <div class="masonry-item">
                <span class="item-tag">UT</span>
                <div class="placeholder" style="padding:70px 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="M21 15l-5-5L5 21" />
                    </svg>
                    Inspeksi UT
                </div>
                <div class="item-overlay"><span class="overlay-label">Ultrasonic Testing</span></div>
            </div>

            <div class="masonry-item">

                <span class="item-tag">MT</span>
                <img src="{{ asset('/template/assets/images/dokumentasi/foto_mt2.JPG') }}"
                    alt="Tim Geotama di lapangan"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" loading="lazy">
                {{-- <div class="placeholder" style="padding:52px 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="M21 15l-5-5L5 21" />
                    </svg>
                    Inspeksi MT
                </div> --}}
                <div class="item-overlay"><span class="overlay-label">Magnetic Particle Testing</span></div>
            </div>

            <div class="masonry-item">
                <span class="item-tag">PT</span>
                <div class="placeholder" style="padding:65px 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="M21 15l-5-5L5 21" />
                    </svg>
                    Inspeksi PT
                </div>
                <div class="item-overlay"><span class="overlay-label">Penetrant Testing</span></div>
            </div>

            <div class="masonry-item">
                <span class="item-tag">UT</span>
                <div class="placeholder" style="padding:55px 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="M21 15l-5-5L5 21" />
                    </svg>
                    Inspeksi UT
                </div>
                <div class="item-overlay"><span class="overlay-label">Ultrasonic Testing</span></div>
            </div>

            <div class="masonry-item">
                <div class="placeholder" style="padding:58px 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="M21 15l-5-5L5 21" />
                    </svg>
                    Foto tim lapangan
                </div>
                <div class="item-overlay"><span class="overlay-label">Tim Lapangan</span></div>
            </div>

            <div class="masonry-item">
                <div class="placeholder" style="padding:44px 16px;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <path d="M21 15l-5-5L5 21" />
                    </svg>
                    Foto tim lapangan
                </div>
                <div class="item-overlay"><span class="overlay-label">Tim Lapangan</span></div>
            </div>

        </div>

        {{-- <div class="gallery-note">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1.5">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 8v4M12 16h.01" />
            </svg>
            Galeri menggunakan layout masonry — setiap foto tampil sesuai ukuran aslinya tanpa dipotong.
        </div> --}}
    </section>

    <!-- CLIENTS -->
    <section class="clients" id="clients">
        <div class="clients-header">
            <div>
                <p class="section-label">Klien Kami</p>
                <h2 class="section-title">Dipercaya oleh Industri</h2>
            </div>
            <div style="text-align: right;">
                <div class="clients-count">68+</div>
                <div class="clients-count-label">Klien Terlayani</div>
            </div>
        </div>
        <div class="clients-grid">
            @for ($i = 1; $i <= 18; $i++)
                <div class="client-logo-placeholder">
                    <img src="{{ asset('template/assets/images/clients/client_' . $i . '.jpg') }}"
                        alt="Client {{ $i }}" style="max-height:50px; max-width:100%; object-fit:contain;"
                        onerror="this.src='{{ asset('template/assets/images/clients/client_' . $i . '.png') }}'"
                        loading="lazy">
                </div>
            @endfor
        </div>
        {{-- <p class="clients-note">* Logo klien akan ditampilkan setelah aset diterima</p> --}}
    </section>

    <!-- CONTACT -->
    <section class="contact" id="contact">
        <div class="contact-grid">
            <div class="contact-info">
                <p class="section-label">Hubungi Kami</p>
                <h2 class="section-title" style="color: white;">Siap Melayani Kebutuhan Inspeksi Anda</h2>
                <p class="contact-desc">Konsultasikan kebutuhan NDT proyek Anda bersama tim ahli Geotama. Kami siap
                    memberikan solusi inspeksi yang tepat dan efisien.</p>
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                        </svg>
                    </div>
                    <div>
                        <div class="contact-item-label">Lokasi</div>
                        <div class="contact-item-val">Batam, Kepulauan Riau, Indonesia</div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="contact-item-label">Telepon / WhatsApp</div>
                        <div class="contact-item-val">+6281270062718</div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="contact-item-label">Email</div>
                        <div class="contact-item-val">info@geotama.co.id</div>
                    </div>
                </div>
            </div>
            <div class="contact-form-box">
                <div class="form-row">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" id="nama" class="form-input" placeholder="Masukkan nama Anda">
                </div>
                <div class="form-row">
                    <label class="form-label">Perusahaan</label>
                    <input type="text" id="perusahaan" class="form-input" placeholder="Nama perusahaan">
                </div>
                <div class="form-row">
                    <label class="form-label">Email</label>
                    <input type="email" id="email" class="form-input" placeholder="email@perusahaan.com">
                </div>
                <div class="form-row">
                    <label class="form-label">Layanan yang Dibutuhkan</label>
                    <select id="layanan" class="form-input" style="cursor: pointer;">
                        <option value="" style="background: #1A3F73;">Pilih layanan NDT</option>
                        <option style="background: #1A3F73;">Magnetic Particle Testing (MT)</option>
                        <option style="background: #1A3F73;">Penetrant Testing (PT)</option>
                        <option style="background: #1A3F73;">Ultrasonic Testing (UT)</option>
                        <option style="background: #1A3F73;">Kombinasi / Paket</option>
                    </select>
                </div>
                <div class="form-row">
                    <label class="form-label">Pesan / Keterangan Proyek</label>
                    <textarea id="pesan" class="form-input" placeholder="Ceritakan kebutuhan inspeksi Anda..."></textarea>
                </div>
                <button onclick="kirimWA()" class="btn-primary"
                    style="width: 100%; padding: 14px; font-size: 15px; border: none;">
                    Kirim via WhatsApp
                </button>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <a href="#" class="footer-brand">
            <div class="footer-logo-icon"><img style="width: 20px; height: auto;"
                    src="{{ asset('/template/assets/images/logo/logo-geotama-removebg-preview.png') }}"
                    alt="Geotama Logo" loading="lazy"></div>
            </div>
            <span class="footer-brand-name">GEOTAMA</span>
        </a>
        <span class="footer-copy">© 2026 Geotama. Semua hak dilindungi.</span>
        <ul class="footer-links">
            <li><a href="#about">Tentang</a></li>
            <li><a href="#services">Layanan</a></li>
            <li><a href="#contact">Kontak</a></li>
        </ul>
    </footer>

    <script>
        const clients = [
            "Klien 01", "Klien 02", "Klien 03", "Klien 04", "Klien 05", "Klien 06",
            "Klien 07", "Klien 08", "Klien 09", "Klien 10", "Klien 11", "Klien 12",
            "Klien 13", "Klien 14", "Klien 15", "Klien 16", "Klien 17", "Klien 18"
        ];
        const grid = document.getElementById('clientGrid');
        clients.forEach(name => {
            const div = document.createElement('div');
            div.className = 'client-logo-placeholder';
            div.textContent = name;
            grid.appendChild(div);
        });

        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const target = document.querySelector(a.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>

    <script>
        function kirimWA() {

            let nama = document.getElementById('nama').value;
            let perusahaan = document.getElementById('perusahaan').value;
            let email = document.getElementById('email').value;
            let layanan = document.getElementById('layanan').value;
            let pesan = document.getElementById('pesan').value;

            // validasi sederhana
            if (!nama || !email) {
                alert("Nama dan Email wajib diisi!");
                return;
            }

            let noWA = "6281270062718";

            let text = `Halo Geotama,%0A%0A
            Nama: ${nama}%0A
            Perusahaan: ${perusahaan}%0A
            Email: ${email}%0A
            Layanan: ${layanan}%0A
            Pesan: ${pesan}%0A%0A
            Saya ingin konsultasi NDT.`;

            let url = `https://wa.me/${noWA}?text=${text}`;

            window.open(url, '_blank');
        }
    </script>

</body>

</html>
