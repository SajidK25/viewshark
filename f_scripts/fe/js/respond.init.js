var owl = $('.response-carousel');
        if (typeof(owl.html()) !== "undefined") {
                owl.owlCarousel({
                items: 4,
                loop: false,
                margin: 10,
                autoplay: false,
                dots: false,
                nav: true,
                navText: [
                        "<i class='icon-chevron-left icon-white icon-share'></i>",
                        "<i class='icon-chevron-right icon-white icon-share'></i>"
                ],
                responsive: {
                0: {
                items: 1
                },
                470: {
                items: 2
                },
                560: {
                items: 1
                },
                700: {
                items: 2
                },
                920: {
                items: 3
                },
                1200: {
                items: 3
                },
                1600: {
                items: 4
                }
        }
        });
        }
