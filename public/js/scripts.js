const screenWidth = window.innerWidth;
console.log(screenWidth)

if(screenWidth>768) {
    const swiper = new Swiper('.swiper-home', {
        direction: 'horizontal',
        loop: true,
        slidesPerView: 2,
        spaceBetween: 50,
        autoplay: {
            delay: 2000,
        },
        pagination: {
            el: '.swiper-pagination',
        },

    });

    if(screenWidth>1200) {
        const swiperKittens = new Swiper('.swiper-kittens', {
            direction: 'horizontal',
            loop: false,
            slidesPerView: 5,
            spaceBetween: 60,
            pagination: {
                el: '.swiper-pagination', // Элемент для пагинации
                clickable: true,          // Возможность переключения слайдов через пагинацию
            },
            navigation: {
                nextEl: '.swiper-button-next', // Кнопка "вперед"
                prevEl: '.swiper-button-prev', // Кнопка "назад"
            },
        });
    }else if(screenWidth>992){
        const swiperKittens = new Swiper('.swiper-kittens', {
            direction: 'horizontal',
            loop: false,
            slidesPerView: 4,
            spaceBetween: 60,
            pagination: {
                el: '.swiper-pagination', // Элемент для пагинации
                clickable: true,          // Возможность переключения слайдов через пагинацию
            },
            navigation: {
                nextEl: '.swiper-button-next', // Кнопка "вперед"
                prevEl: '.swiper-button-prev', // Кнопка "назад"
            },
        });
    } else{
        const swiperKittens = new Swiper('.swiper-kittens', {
            direction: 'horizontal',
            loop: false,
            slidesPerView: 3,
            spaceBetween: 60,
            pagination: {
                el: '.swiper-pagination', // Элемент для пагинации
                clickable: true,          // Возможность переключения слайдов через пагинацию
            },
            navigation: {
                nextEl: '.swiper-button-next', // Кнопка "вперед"
                prevEl: '.swiper-button-prev', // Кнопка "назад"
            },
        });
    }
}
else{
    const swiper = new Swiper('.swiper-home', {
        direction: 'horizontal',
        loop: true,
        slidesPerView: 1,
        autoplay: {
            delay: 2000,
        },
        pagination: {
            el: '.swiper-pagination',
        },

    });

    const swiperKittens = new Swiper('.swiper-kittens', {
        direction: 'horizontal',
        loop: false,
        slidesPerView: 5,
        spaceBetween: 60,
        pagination: {
            el: '.swiper-pagination', // Элемент для пагинации
            clickable: true,          // Возможность переключения слайдов через пагинацию
        },
        navigation: {
            nextEl: '.swiper-button-next', // Кнопка "вперед"
            prevEl: '.swiper-button-prev', // Кнопка "назад"
        },
    });
}



// const input = document.querySelector("#request_phone");
// window.intlTelInput(input, {
//     utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/utils.js",
// });

function selectLitter(keyWord, litterId){
    fetch(`/available-kittens/${keyWord}/${litterId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            document.getElementById('litter-block').innerHTML = `
                <div class="title d-flex flex-column align-items-center justify-content-center">
                    <h2>LITTER ${data.litter.name}</h2>
                    <img src="/img/arrow/simple-arrow.png" alt="arrow">
                    <p>${new Date(data.litter.date).toLocaleDateString()}</p>
                </div>
                <div class="section-inner d-flex justify-content-center align-items-start litter__parents">
                    <div class="col-3 cat-parent d-flex flex-column align-items-center">
                        <div class="image-container shaded-green">
                            <img src="/img/cats/${data.mother.imageLink}" alt="Mother Cat">
                        </div>
                        <h4>${data.mother.name}</h4>
                    </div>
                    <div class="parent-divider">
                        <img src="/img/arrow/litter-divider-lg.png" alt="arrow">
                    </div>
                    <div class="col-3 cat-parent d-flex flex-column align-items-center">
                        <div class="image-container shaded-green">
                            <img src="/img/cats/${data.father.imageLink}" alt="Mother Cat">
                        </div>
                        <h4>${data.father.name}</h4>
                    </div>
                </div>
                <div class="section-white container section d-flex flex-wrap section-kittens justify-content-center">
                ${data.kittens.map(kitten => {
                    let classStatus = '';
                    if(kitten.kittenStatus === "At home"){
                        classStatus = "status-home";
                    }
                    else if(kitten.kittenStatus === "Reserved"){
                        classStatus = "status-reserved";
                    }
                    else if(kitten.kittenStatus === "Available"){
                        classStatus = "status-available";
                    }
                    return `
                        <div class="col-3 d-flex flex-column align-items-center">
                            <div class="image-container">
                                <img src="/img/kittens/${kitten.imageLink}" alt="${kitten.name}-kitten">
                            </div>    
                            <h4>${kitten.name}</h4>
                            <div class="kitten-status ${classStatus}">
                                <span>${kitten.kittenStatus}</span>
                            </div>    
                        </div>
                    `;
                }).join('')}
                </div>
            `;
        })
    .catch(error => console.error('Error: ', error));    
}