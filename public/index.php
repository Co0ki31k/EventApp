<?php
require_once "../config/paths.php";
require_once "../includes/header.php";
?>

<div class="main-rounded-container">
    <?php require_once "../includes/navbar.php"; ?>
    <!-- Hero: headline, short description and centered CTA -->
    <section id="hero" class="hero">
        <div class="hero-inner">
            <h1>Znajdź perfekcyjne<br>wydarzenie dla Ciebie</h1>
            <p>Przeglądaj koncerty, warsztaty, festiwale i wiele więcej na interaktywnej mapie.</p>
            <p>Nigdy więcej nie przegap tego, co dzieje się w Twoim mieście.</p>
            <a class="hero-cta" href="<?= isLoggedIn() ? 'map.php' : 'map_guest.php' ?>">Przejdź do mapy</a>
        </div>
    </section>

    <!-- Statystyki pod hero -->
    <div class="stats-row">
        <div class="stat-block">
            <div class="stat-number">10K+</div>
            <div class="stat-label">Wydarzeń</div>
        </div>
        <div class="stat-block">
            <div class="stat-number">50K+</div>
            <div class="stat-label">Użytkowników</div>
        </div>
        <div class="stat-block">
            <div class="stat-number">100+</div>
            <div class="stat-label">Miast</div>
        </div>
        <div class="stat-block">
            <div class="stat-number">4.9</div>
            <div class="stat-label">Ocena</div>
        </div>
    </div>
    
<div class="sections-wrapper">

    <!-- Spacer and three feature cards (image + title + description) -->
    <div id="features" class="features-section">
        <div class="features-row">
            <article class="feature-card">
                <div class="feature-media" style="background-image: url('/EventApp/assets/img/feature1.png')"></div>
                <h3>Przeglądaj mapę</h3>
                <p>Sprawdź wydarzenia w Twojej okolicy lub wyszukuj w innych miastach</p>
            </article>
            <article class="feature-card">
                <div class="feature-media" style="background-image: url('/EventApp/assets/img/feature2.png')"></div>
                <h3>Wybierz wydarzenie</h3>
                <p>Znajdź coś, co Cię interesuje i sprawdź wszystkie szczegóły</p>
            </article>
            <article class="feature-card">
                <div class="feature-media" style="background-image: url('/EventApp/assets/img/feature3.png')"></div>
                <h3>Dołącz i baw się</h3>
                <p>Zapisz się na wydarzenie i ciesz się wspaniałymi chwilami</p>
            </article>
        </div>
    </div>
    <!-- Dla Firm: sekcja skierowana do klientów biznesowych -->
    <section id="business" class="business-section">
        <div class="business-wrapper">
            <div class="business-inner">
            <h2>Dla firm</h2>
            <p class="lead">Organizujesz wydarzenia lub chcesz promować ofertę firmową? Skorzystaj z naszych narzędzi do zarządzania i promocji.</p>

            <div class="business-cards">
                <article class="feature-card biz-card">
                    <div class="card">
                        <div class="card-inner">
                            <div class="card-front">
                                <div class="card-content">
                                    <h3>Promocja wydarzeń</h3>
                                </div>
                            </div>
                            <div class="card-back">
                                    <div class="card-content">
                                        <p>Zwiększ zasięg wydarzeń dzięki rekomendacjom i promocjom na mapie.</p>
                                    </div>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="feature-card biz-card">
                    <div class="card">
                        <div class="card-inner">
                            <div class="card-front">
                                <div class="card-content">
                                    <h3>Zarządzanie uczestnikami</h3>
                                </div>
                            </div>
                            <div class="card-back">
                                    <div class="card-content">
                                        <p>Łatwe narzędzia do zapisów, limitów i komunikacji z uczestnikami.</p>
                                    </div>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="feature-card biz-card">
                    <div class="card">
                        <div class="card-inner">
                            <div class="card-front">
                                <div class="card-content">
                                    <h3>Pakiety partnerskie</h3>
                                </div>
                            </div>
                            <div class="card-back">
                                    <div class="card-content">
                                        <p>Pakiety reklamowe i partnerskie dopasowane do Twoich potrzeb.</p>
                                    </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Slider partners (logotypy firm reklamowych) - CSS infinite slider -->
            <div class="partners-inner">
                <div id="infinite" class="highway-slider partners-wrapper">
                    <div class="container highway-barrier">
                        <ul class="highway-lane">
                            <li class="highway-car"><img src="/EventApp/assets/img/partner1.png" alt="Partner 1"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner2.png" alt="Partner 2"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner3.png" alt="Partner 3"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner4.png" alt="Partner 4"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner5.png" alt="Partner 5"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner6.png" alt="Partner 6"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner7.png" alt="Partner 7"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner8.png" alt="Partner 8"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner9.png" alt="Partner 9"></li>

                            <!-- duplicated sequence for continuous scroll -->
                            <li class="highway-car"><img src="/EventApp/assets/img/partner1.png" alt="Partner 1"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner2.png" alt="Partner 2"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner3.png" alt="Partner 3"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner4.png" alt="Partner 4"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner5.png" alt="Partner 5"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner6.png" alt="Partner 6"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner7.png" alt="Partner 7"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner8.png" alt="Partner 8"></li>
                            <li class="highway-car"><img src="/EventApp/assets/img/partner9.png" alt="Partner 9"></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="business-cta"><a class="biz-cta" href="#">Skontaktuj się</a></div>

            <script src="/EventApp/assets/js/highwayResize.js"></script>
            <script src="/EventApp/assets/js/navCenterScroll.js"></script>

        </div> <!-- .business-inner -->
    </div> <!-- .business-wrapper -->
    </section> <!-- #business -->

    <?php require_once "../includes/footer_block.php"; ?>

    </div> <!-- .sections-wrapper -->

</div> <!-- .main-rounded-container -->

