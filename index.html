<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма заявки</title>
</head>
<body>
    <h1>Форма заявки</h1>
    <form id="leadForm">
        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="phone">Телефон:</label>
        <input type="text" id="phone" name="phone" required><br><br>

        <label for="price">Цена:</label>
        <input type="number" id="price" name="price" required><br><br>

        <input type="submit" value="Отправить">
    </form>

    <script>
        let timeSpent = false;

        setTimeout(() => {
            timeSpent = true; // Пользователь провел на сайте больше 30 секунд
        }, 30000);

        document.getElementById("leadForm").addEventListener("submit", async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            const data = {
                name: formData.get("name"),
                email: formData.get("email"),
                phone: formData.get("phone"),
                price: formData.get("price"),
                timeSpent: timeSpent ? 1 : 0 // передаем информацию о времени
            };

            const response = await fetch("/send_to_amocrm.php", {
                method: "POST",
                body: JSON.stringify(data),
                headers: {
                    "Content-Type": "application/json"
                }
            });

            if (response.ok) {
                console.log(response);
                alert("Заявка успешно отправлена!");
            } else {
                alert("Произошла ошибка при отправке заявки.");
            }
        });
    </script>
</body>
</html>