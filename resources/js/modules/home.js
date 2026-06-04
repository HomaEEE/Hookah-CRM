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
        duration: 20,     // Speed of the marquee (lower is faster)
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
    }, { amount: 0.01 });

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
       7. ПОЯВЛЕНИЕ ТЕКСТА ПО СЛОВАМ (Универсально)
       HTML: <h2 class="text-reveal">Ваш текст</h2>
       ========================================================================== */
    const textElements = document.querySelectorAll(".text-reveal");

    textElements.forEach(textElement => {
        // Разбиваем текст на слова (учитывая лишние пробелы)
        const words = textElement.textContent.trim().split(/\s+/);

        // Оборачиваем каждое слово
        textElement.innerHTML = words
            .map(word => `<span style="display: inline-block; opacity: 0; transform: translateY(20px); will-change: transform, opacity;">${word}</span>`)
            .join(" ");

        // Вешаем триггер на этот конкретный текстовый блок
        inView(textElement, () => {
            animate(
                textElement.querySelectorAll("span"),
                { opacity: 1, y: 0 },
                {
                    delay: stagger(0.04), // Для текста задержку лучше делать меньше (0.04-0.08)
                    duration: 0.6,
                    ease: [0.17, 0.55, 0.55, 1] // Та же премиальная кривая
                }
            );
        }, { amount: 0.2 });
    });

});