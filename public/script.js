
        let timeSpent = false;

        // Отслеживаем, если пользователь проводит на странице больше 30 секунд
        setTimeout(() => {
            timeSpent = true; // Пользователь провел на сайте больше 30 секунд
        }, 30000);

        document.getElementById("leadForm").addEventListener("submit", async function(e) {
            e.preventDefault();

            // Собираем данные формы
            const formData = new FormData(this);

            const data = {
                name: formData.get("name"),
                email: formData.get("email"),
                phone: formData.get("phone"),
                price: formData.get("price"),
                timeSpent: timeSpent ? 1 : 0 // Передаем информацию о времени
            };

            try {
                // Отправляем данные на сервер с помощью fetch
                const response = await fetch("/send_to_amocrm.php", {
                    method: "POST",
                    body: JSON.stringify(data),
                    headers: {
                        "Content-Type": "application/json"
                    }
                });

                // Читаем и парсим ответ от сервера
                const responseData = await response.json();

                // Выводим данные в консоль
                console.log('Ответ сервера:', responseData);

                if (response.ok) {
                    alert("Заявка успешно отправлена!");
                } else {
                    alert("Произошла ошибка при отправке заявки.");
                }
            } catch (error) {
                // Обработка ошибок
                console.error('Ошибка при отправке данных:', error);
                alert("Произошла ошибка при отправке заявки.");
            }
        });
