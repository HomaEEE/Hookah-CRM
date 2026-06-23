// Добавлен импорт stagger
import { scroll, animate, inView, stagger } from "motion";

document.addEventListener('DOMContentLoaded', () => {


    /* ==========================================================================
       1. БАЗОВЫЕ НАСТРОЙКИ
       ========================================================================== */
    const softFloatOptions = (duration, delay) => ({
        duration: duration,
        delay: delay,
        repeat: Infinity,
        direction: "alternate",
        easing: "ease-in-out"
    });

    const track = document.querySelector(".marquee-track");

    animate(
    track,
    { x: ["0%", "-50%"] }, // Move from start to half-way (the end of the first clone)
    {
        duration: 15,     // Speed of the marquee (lower is faster)
        repeat: Infinity, // Loop forever
        ease: "linear",   // Maintain constant velocity
    }
    );


    /* ==========================================================================
       2. УВЕЛИЧЕНИЕ ПРИ СКРОЛЛЕ
       ========================================================================== */
    scroll(animate("#coal-1", { scale: [0.9, 1] }));
    scroll(animate("#coal-2", { scale: [0.9, 1] }));
    scroll(animate("#coal-3", { scale: [0.9, 1] }));

    /* ==========================================================================
       3. БЕСКОНЕЧНАЯ ЛЕВИТАЦИЯ УГЛЕЙ
       ========================================================================== */
    animate("#coal-1", { y: [0, -5, 0], x: [0, 2, 0], rotate: [0, 1, 0] }, softFloatOptions(4.5, 0));
    animate("#coal-2", { y: [0, 4, 0], x: [0, -3, 0], rotate: [0, -1, 0] }, softFloatOptions(5.8, 0.4));
    animate("#coal-3", { y: [0, -4, 0], x: [0, -4, 0], rotate: [0, 1.5, 0] }, softFloatOptions(3.9, 0.8));

    /* ==========================================================================
       4. ВРАЩЕНИЕ ТЕКСТА ПО СКРОЛЛУ
       ========================================================================== */
    scroll(animate(".rotate-clockwise", { rotate: [0, 45] }));
    scroll(animate(".rotate-counterclockwise", { rotate: [0, -45] }));

    /* ==========================================================================
       5. ПРОСТЫЕ inView АНИМАЦИИ
       ========================================================================== */
    inView(".fade-up", (element) => {
        animate(
            element,
            { opacity: 1, y: [100, 0] },
            {
                duration: 1,
                easing: [0.17, 0.55, 0.55, 1],
            }
        );
    }, { amount: 0.005 });

    inView(".fade-right", (element) => {
        animate(
            element,
            { opacity: 1, x: [100, 0] },
            {
                duration: 1,
                easing: [0.17, 0.55, 0.55, 1],
            }
        );
        console.log("The link ", element, " has entered the viewport")
    }, { amount: 0.1 });

    /* ==========================================================================
       6. КАСКАДНОЕ ПОЯВЛЕНИЕ (Сетки и списки)
       ========================================================================== */
    inView(".stagger-group", (element) => {
        const items = element.querySelectorAll(".stagger-item");
        if (items.length > 0) {
            animate(
                items,
                { opacity: 1, y: 0 },
                {
                    duration: 0.6,
                    delay: stagger(0.15),
                    ease: [0.17, 0.55, 0.55, 1]
                }
            );
        }
    }, { amount: 0.15 });

    /* ==========================================================================
       <h2 class="text-reveal">Text</h2>
       ========================================================================== */
function initTextReveal() {
    const textElements = document.querySelectorAll(".text-reveal");

    textElements.forEach(textElement => {
        // 1. Разбиваем текст на слова, удаляя лишние пробелы
        const words = textElement.textContent.trim().split(/\s+/);

        // 2. Очищаем родительский контейнер
        textElement.innerHTML = "";

        // 3. Оборачиваем каждое слово для эффекта маски
        words.forEach(word => {
            // Внешний span (работает как маска/рамка, скрывающая слово до анимации)
            const wrapper = document.createElement("span");
            wrapper.style.display = "inline-block";
            wrapper.style.overflow = "hidden"; // Вырезает все, что выходит за границы
            wrapper.style.marginRight = "0.25em"; // Сохраняем пробелы между словами
            wrapper.style.verticalAlign = "bottom";

            // Внутренний span (само слово, которое будет двигаться)
            const inner = document.createElement("span");
            inner.textContent = word;
            inner.className = "reveal-word"; // Класс для таргетинга анимацией
            inner.style.display = "inline-block";
            inner.style.willChange = "transform, opacity, filter";

            // Задаем строгие стартовые стили, чтобы избежать мерцания
            inner.style.opacity = "0";
            inner.style.transform = "translateY(100%) rotate(3deg)";
            inner.style.filter = "blur(4px)";

            wrapper.appendChild(inner);
            textElement.appendChild(wrapper);
        });

        // 4. Запускаем анимацию при скролле
        inView(textElement, () => {
            const targets = textElement.querySelectorAll(".reveal-word");

            animate(
                targets,
                {
                    // Жестко передаем массивы [От, До], чтобы Motion точно знал состояния
                    opacity: [0, 1],
                    y: ["100%", "0%"],
                    rotate: [3, 0], // Легкое выравнивание в процессе появления
                    filter: ["blur(4px)", "blur(0px)"]
                },
                {
                    delay: stagger(0.06), // Идеальный тайминг для заголовков
                    duration: 0.8,
                    ease: [0.16, 1, 0.3, 1] // Премиальная кривая (custom spring/out)
                }
            );
        }, {
            // Триггер сработает, когда блок покажется хотя бы на 10%
            margin: "-10% 0px -10% 0px"
        });
    });
}

// Запуск (если вы не используете фреймворки, просто вызовите функцию)
document.addEventListener('DOMContentLoaded', initTextReveal);
});