<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ asset('landing/css/style.min.css') }}">
    <title>CASVA</title>
</head>
<body>


<div class="wrapper">
    <section class="first__section">
        <header class="header">
            <div class="header__wrapper">
                <div class="header__container _container">
                    <div class="header__body">
                        <a href="" class="header__logo">
                            <img src="{{ asset('landing/img/logotip.png') }}" alt="">
                        </a>
{{--                        <div class="header__menu menu">--}}
{{--                            <ul class="menu__list">--}}
{{--                                <li class="menu__item"><a href="" class="menu_link">О компании</a></li>--}}
{{--                                <li class="menu__item"><a href="" class="menu_link">Мобильное приложение</a></li>--}}
{{--                                <li class="menu__item"><a href="" class="menu_link web_version">Веб версия</a></li>--}}
{{--                                <li class="menu__item"><a href="" class="menu_link">Приложение для водителей</a></li>--}}
{{--                                <li class="menu__item"><a href="" class="menu_link">Партнеры</a></li>--}}
{{--                            </ul>--}}
{{--                        </div>--}}
{{--                        <div class="header__select">--}}
{{--                            <div class="select_wrap">--}}
{{--                                <ul class="default_option ">--}}
{{--                                    <li class="option">--}}
{{--                                        <img src="{{ asset('landing/img/russian.jpg') }}" alt="">--}}
{{--                                        <p>Русский</p>--}}
{{--                                    </li>--}}
{{--                                    <span class="_icon-arrow-down"></span>--}}
{{--                                </ul>--}}
{{--                                <ul class="select_ul">--}}
{{--                                    <li class="option">--}}
{{--                                        <img src="{{ asset('landing/img/russian.jpg') }}" alt="">--}}
{{--                                        <p>Русский</p>--}}
{{--                                    </li>--}}
{{--                                    <li class="option">--}}
{{--                                        <img src="{{ asset('landing/img/united-states.jpg') }}" alt="">--}}
{{--                                        <p>Английский</p>--}}
{{--                                    </li>--}}
{{--                                    <li class="option">--}}
{{--                                        <img src="{{ asset('landing/img/uzbekistan.jpg') }}" alt="">--}}
{{--                                        <p>Узбекский</p>--}}
{{--                                    </li>--}}
{{--                                </ul>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <button type="button" class="header__burger">--}}
{{--                            <span></span>--}}
{{--                            <span></span>--}}
{{--                            <span></span>--}}
{{--                        </button>--}}
                    </div>
                </div>
            </div>
        </header>
        <div class="fcontent">
            <div class="fcontent__container _container">
                <div class="fcontent__body">
                    <div class="fcontent__cont">
                        <h1 class="fcontent__cont-title">
                            ТЕХНОЛОГИИ ГРУЗОПЕРЕВОЗОК
                        </h1>
                        <h2 class="fcontent__cont-sub_title">
                            Тендерная Платформа по организации перевозок грузов
                        </h2>
                        <a href="" class="fcontent__cont-btn btn">
                            Войти в личный кабинет CASVA
                        </a>
                        <p class="fcontent__cont-text">Скачивайте приложение и закажите доставку груза всего за 2 минуты</p>
                        <div class="casva__download-links">
                            <a href="">
                                <img src="{{ asset('landing/img/app-store-apple.png') }}" alt="">
                            </a>
                            <a href="">
                                <img src="{{ asset('landing/img/google-play-badge.png') }}" alt="">
                            </a>
                        </div>
                    </div>
                    <div class="fcontent__right">
                        <img src="{{ asset('landing/img/mokapbooksmartfon.png') }}" alt="">
                    </div>
                </div>
            </div>
            <a href="#logistic__service" class="fcontent__GtoBottom arrow__btn _icon-arrow-down"></a>
        </div>
        <img class="fsection_1-1" src="{{ asset('landing/img/fsection1.1.png') }}" alt="">
        <img class="fsection_1-2" src="{{ asset('landing/img/fsection1.2.png') }}" alt="">
        <img class="fsection_1-3" src="{{ asset('landing/img/shape.png') }}" alt="">

    </section>
    <section id="logistic__service" class="logistic__service ls">
        <div class="ls__container _container">
            <div class="logistic-service__body">
                <div class="section__header">
                    <h2 class="sectionH__title">
                        Логистическеий сервис CASVA
                    </h2>
                    <h3 class="sectionH__sub-title">
                        НОВЫЙ СТАНДАРТ ПЕРЕВОЗОК
                    </h3>
                </div>
                <div class="ls__content">
                    <div class="ls__content-left">
                        <h4 class="lsc__left-title">
                            Личный кабинет заказчика
                        </h4>
                        <p class="lsc__left-text">
                            Создание заявки - определяйте условия и цену перевозки самостоятельно
                        </p>
                        <h4 class="lsc__left-title">
                            БЫСТРЫЙ ПРОСМОТР ЗАЯВОК
                        </h4>
                        <p class="lsc__left-text">
                            Создание заявки - определяйте условия и цену перевозки самостоятельно
                        </p>
                        <h4 class="lsc__left-title">
                            Геолокация заказа
                        </h4>
                        <p class="lsc__left-text">
                            Отслеживание собственного и наёмного транспорта на одной карте <br>Просмотр треков движения <br> Система оповещений о погрузке и выгрузке
                        </p>
                    </div>
                    <div class="ls__content-slider">
                        <div class="swiper ls__swipper">
                            <div class="swiper-wrapper ls__swipper-wrapper">
                                <div class="swiper-slide ls__swipper-slide">
                                    <img src="{{ asset('landing/img/logistic-services/logisticServices1.png') }}" alt="">
                                </div>
                                <div class="swiper-slide ls__swipper-slide">
                                    <img src="{{ asset('landing/img/logistic-services/logisticServices2.png') }}" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="ls__swipper-prev arrow__btn _icon-arrow-down"></div>
                        <div class="ls__swipper-next arrow__btn _icon-arrow-down"></div>
                    </div>
                </div>
                <div class="ls__footer section__footer">
                    <a href="" class="btn">Подробнее</a>
                </div>
            </div>
        </div>

    </section>
    <section class="phone__app pApp">
        <div class="pApp__body">
            <div class="section__header">
                <h2 class="sectionH__title">
                    Приложение для смартфона
                </h2>
                <h3 class="sectionH__sub-title">
                    Работайте на любом устройстве iOS или Android
                </h3>
            </div>
            <div class="pApp__content">
                <div class="pAppCont__text1">
                    <div class="pAppCont__text1-title">
                        Создание заявки
                    </div>
                    <p class="pAppCont__text1-text">
                        Определяйте условия и цену перевозки самостоятельно
                    </p>
                </div>
                <div class="pAppCont__text2">
                    <div class="pAppCont__text2-title">
                        CASVA
                    </div>
                    <p class="pAppCont__text2-text">
                        Ваш надёжный проводник в мире логистики!
                    </p>
                </div>
                <div class="pApp__vektor1">
                    <img src="{{ asset('landing/img/pApp/phoneAppVektor1.svg') }}" alt="">
                </div>
                <img class="pAppCont__text1-img" src="{{ asset('landing/img/pApp/phoneAppCreateImg1.png') }}" alt="">
                <img class="imgArrow1" src="{{ asset('landing/img/fsection1.1.png') }}" alt="">
                <img class="imgArrow2" src="{{ asset('landing/img/fsection1.2.png') }}" alt="">
                <div class="pApp__slider">
                    <div class="swipper pApp__swipper">
                        <div class="swiper-wrapper pApp__swipper-wrapper">
                            <div class="swiper-slide pApp__swipper-slide">
                                <img src="{{ asset('landing/img/pApp/pAppSilder1.png') }}" alt="">
                            </div>
                            <div class="swiper-slide pApp__swipper-slide">
                                <img src="{{ asset('landing/img/pApp/pAppSilder2.png') }}" alt="">
                            </div>
                            <div class="swiper-slide pApp__swipper-slide">
                                <img src="{{ asset('landing/img/pApp/pAppSilder2.png') }}" alt="">
                            </div>
                            <div class="swiper-slide pApp__swipper-slide">
                                <img src="{{ asset('landing/img/pApp/pAppSilder2.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="pApp__swipper-actions">
                        <div class="pApp__swipper-prev arrow__btn _icon-arrow-down"></div>
                        <div class="pApp__swipper-pagination"></div>
                        <div class="pApp__swipper-next arrow__btn _icon-arrow-down"></div>
                    </div>
                    <div class="pApp__vektor2">
                        <img src="{{ asset('landing/img/pApp/phoneAppVektor2.svg') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="pApp__footer section__footer">
                <div class="pApp__footer-text">
                    Скачивайте приложение и закажите доставку груза всего за 2 минуты
                </div>
                <a href="">
                    <img src="{{ asset('landing/img/google-play-badge.png') }}" alt="">
                </a>
                <a href="">
                    <img src="{{ asset('landing/img/app-store-apple.png') }}" alt="">
                </a>
            </div>
        </div>
    </section>
    <section class="accOn__Computer AccComp">
        <div class="AccComp__container _container">
            <div class="AccComp__body">
                <div class="section__header">
                    <h2 class="sectionH__title">
                        Личный кабинет на компьютере
                    </h2>
                    <h3 class="sectionH__sub-title">
                        УДОБНЫЙ ВЕБ-ИНТЕРФЕЙС СЕРВИСА
                    </h3>
                </div>
                <div class="AccComp__slider">
                    <div class="swipper AccComp__swipper">
                        <div class="swiper-wrapper AccComp__swipper-wrapper">
                            <div class="swiper-slide AccComp__swipper-slide AccCompSwSlide">
                                <div class="AccCompSwSlide__img">
                                    <img src="{{ asset('landing/img/AccComp/AccCompSlide1.png') }}" alt="">
                                </div>
                                <div class="AccCompSwSlide__text">
                                    <div class="AccCompSwSlide__text-title">
                                        Личный кабинет заказчика на сайте <span>www.CASVA.uz</span>
                                    </div>
                                    <p class="AccCompSwSlide__text-text">
                                        Создание заявки - определяйте условия и цену перевозки самостоятельно
                                        <br><br>
                                        Создание заявки - определяйте условия и цену перевозки самостоятельно
                                        <br><br>
                                        Отслеживание собственного и наёмного транспорта на одной карте <br>Просмотр треков движения<br>Система оповещений о погрузке и выгрузке
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="AccComp__swipper-prev arrow__btn _icon-arrow-down"></div>
                    <div class="AccComp__swipper-next arrow__btn _icon-arrow-down"></div>
                </div>
                <div class="AccComp__footer section__footer">
                    <a href="" class="btn">Войти в личный кабинет CASVA</a>
                </div>
            </div>
        </div>
    </section>
    <section class="appFor__drivers appFdr">
        <div class="appFdr__body">
            <div class="section__header">
                <h2 class="sectionH__title">
                    Приложение для водителей
                </h2>
                <h3 class="sectionH__sub-title">
                    Присоединяйтесь к НАМ И ПОЛУЧАЙТЕ ЗАКАЗЫ С ПЕРВОГО ДНЯ РАБОТЫ
                </h3>
            </div>
            <div class="appFdr__content">
                <div class="appFdrCont__text1">
                    <div class="appFdrCont__text1-title">
                        Создание заявки
                    </div>
                    <p class="appFdrCont__text1-text">
                        Определяйте условия и цену перевозки самостоятельно
                    </p>
                </div>
                <div class="appFdrCont__text2">
                    <div class="appFdrCont__text2-title">
                        CASVA
                    </div>
                    <p class="appFdrCont__text2-text">
                        Ваш надёжный проводник в мире логистики!
                    </p>
                </div>
                <div class="appFdr__vektor1">
                    <img src="{{ asset('landing/img/pApp/phoneAppVektor1.svg') }}" alt="">
                </div>
                <img class="appFdrCont__text1-img" src="{{ asset('landing/img/pApp/phoneAppCreateImg1.png') }}" alt="">
                <img class="imgArrow1" src="{{ asset('landing/img/fsection1.1.png') }}" alt="">
                <img class="imgArrow2" src="{{ asset('landing/img/fsection1.2.png') }}" alt="">
                <div class="appFdr__slider">
                    <div class="swipper appFdr__swipper">
                        <div class="swiper-wrapper appFdr__swipper-wrapper">
                            <div class="swiper-slide appFdr__swipper-slide">
                                <img src="{{ asset('landing/img/pApp/pAppSilder1.png') }}" alt="">
                            </div>
                            <div class="swiper-slide appFdr__swipper-slide">
                                <img src="{{ asset('landing/img/pApp/pAppSilder2.png') }}" alt="">
                            </div>
                            <div class="swiper-slide appFdr__swipper-slide">
                                <img src="{{ asset('landing/img/pApp/pAppSilder2.png') }}" alt="">
                            </div>
                            <div class="swiper-slide appFdr__swipper-slide">
                                <img src="{{ asset('landing/img/pApp/pAppSilder2.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="appFdr__swipper-actions">
                        <div class="appFdr__swipper-prev arrow__btn _icon-arrow-down"></div>
                        <div class="appFdr__swipper-pagination"></div>
                        <div class="appFdr__swipper-next arrow__btn _icon-arrow-down"></div>
                    </div>
                    <div class="appFdr__vektor2">
                        <img src="{{ asset('landing/img/pApp/phoneAppVektor2.svg') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="appFdr__footer section__footer">
                <div class="appFdr__footer-text">
                    Скачивайте приложение и закажите доставку груза всего за 2 минуты
                </div>
                <a href="">
                    <img src="{{ asset('landing/img/google-play-badge.png') }}" alt="">
                </a>
                <a href="">
                    <img src="{{ asset('landing/img/app-store-apple.png') }}" alt="">
                </a>
            </div>
        </div>
    </section>
    <section class="whyCustchCasva">
        <div class="whyCustchCasva__container _container">
            <div class="whyCustchCasva__body">
                <div class="section__header">
                    <h2 class="sectionH__title">
                        Почему клиенты выбирают «CASVA»
                    </h2>
                    <h3 class="sectionH__sub-title">
                        Наше лучшее приложение для вашего Бизнеса
                    </h3>
                </div>
                <div class="whyCustchCasva__content">
                    <div class="whyCustchCasvaCont__item">
                        <div class="whyCustchCasvaCont__item-img">
                            <img src="{{ asset('landing/img/whyCustchCasva/whyCustchCasva-img1.png') }}" alt="">
                        </div>
                        <div class="whyCustchCasvaCont__item-text">
                            Автоматизация и планирование перевозок
                        </div>
                    </div>
                    <div class="whyCustchCasvaCont__item">
                        <div class="whyCustchCasvaCont__item-img">
                            <img src="{{ asset('landing/img/whyCustchCasva/whyCustchCasva-img2.png') }}" alt="">
                        </div>
                        <div class="whyCustchCasvaCont__item-text">
                            Обширная база контрагентов
                        </div>
                    </div>
                    <div class="whyCustchCasvaCont__item">
                        <div class="whyCustchCasvaCont__item-img">
                            <img src="{{ asset('landing/img/whyCustchCasva/whyCustchCasva-img3.png') }}" alt="">
                        </div>
                        <div class="whyCustchCasvaCont__item-text">
                            Моментальное информирование в личном кабинете
                        </div>
                    </div>
                    <div class="whyCustchCasvaCont__item">
                        <div class="whyCustchCasvaCont__item-img">
                            <img src="{{ asset('landing/img/whyCustchCasva/whyCustchCasva-img4.png') }}" alt="">
                        </div>
                        <div class="whyCustchCasvaCont__item-text">
                            Прямая связь между заказчиком и перевозчиком
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="ourPartners">
        <div class="ourPartners__container _container">
            <div class="ourPartners__body">
                <div class="section__header">
                    <h2 class="sectionH__title">
                        Наши партнеры
                    </h2>
                </div>
            </div>
            <div class="ourPartners__slider">
                <div class="swipper ourPartners__swipper">
                    <div class="swiper-wrapper ourPartners__swipper-wrapper">
                        <a href="/" class="swiper-slide ourPartners__swipper-slide ourPartSwItem">
                            <div class="ourPartSwItem__img">
                                <img src="{{ asset('landing/img/ourPartnersSliderItem1.png') }}" alt="">
                            </div>
                        </a>
                        <a href="/" class="swiper-slide ourPartners__swipper-slide ourPartSwItem">
                            <div class="ourPartSwItem__img">
                                <img src="{{ asset('landing/img/ourPartnersSliderItem1.png') }}" alt="">
                            </div>
                        </a>
                        <a href="/" class="swiper-slide ourPartners__swipper-slide ourPartSwItem">
                            <div class="ourPartSwItem__img">
                                <img src="{{ asset('landing/img/ourPartnersSliderItem1.png') }}" alt="">
                            </div>
                        </a>
                        <a href="/" class="swiper-slide ourPartners__swipper-slide ourPartSwItem">
                            <div class="ourPartSwItem__img">
                                <img src="{{ asset('landing/img/ourPartnersSliderItem1.png') }}" alt="">
                            </div>
                        </a>
                    </div>
                </div>
                <div class="ourPartners__swipper-prev arrow__btn _icon-arrow-down"></div>
                <div class="ourPartners__swipper-next arrow__btn _icon-arrow-down"></div>
            </div>
        </div>
    </section>
    <footer class="footer">
        <div class="footer__container _container">
            <div class="footer__body body-footer">
                <div class="body-footer__top">
                    <div class="body-footer__left">
                        <div class="footer__logo">
                            <img src="{{ asset('landing/img/logotip.png') }}" alt="logo">
                        </div>
                        <div class="footer__text">
                            Наша цель – построить прозрачный, долгосрочный бизнес, приносить огромную пользу населению, путем решения логистических вопросов.
                        </div>
                        <div class="footer__link section__footer">
                            <a href="" class="btn">Войти в личный кабинет CASVA</a>
                        </div>
                    </div>
                    <div class="body-footer__right footer-right">
                        <h3 class="footer-right__title">
                            Скачивайте приложение и закажите доставку груза всего за 2 минуты
                        </h3>
                        <div class="footer-right__stores">
                            <a href="">
                                <img src="{{ asset('landing/img/google-play-badge.png') }}" alt="">
                            </a>
                            <a href="">
                                <img src="{{ asset('landing/img/app-store-apple.png') }}" alt="">
                            </a>
                        </div>
                        <div class="footer-right__contacts footer-contact">
                            <a href="/" class="footer-contact__item contact-location">г. Самарканд, ул. Спитаменшох 270</a>
                            <a href="/" class="footer-contact__item contact-call">+998 97 916-66-40 <br> +998 90 228-87-73</a>
                            <a href="/" class="footer-contact__item contact-email">info@casva.uz</a>
                        </div>
                    </div>
                </div>
                <div class="body-footer__bottom footer-bottom">
                    <div class="footer-bottom__text">
                        © 2021 ООО «Casva Software»
                    </div>
                    <div class="footer-bottom__social social-footer">
                        Мы в соц сетях:
                        <a href="/" class="social-footer__item _icon-instagram"></a>
                        <a href="/" class="social-footer__item _icon-facebook"></a>
                        <a href="/" class="social-footer__item _icon-telegram"></a>
                        <a href="/" class="social-footer__item _icon-youtube"></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('landing/js/script.min.js') }}"></script>
</body>
</html>
