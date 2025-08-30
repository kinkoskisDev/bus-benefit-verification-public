
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            background-color: #e2e8f0;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .email-container {
            background: #fff;
            max-width: 600px;
            margin: 40px auto;
            padding: 40px;
            text-align: center;
        }
        .email-footer {
            background-color: #1e293b;
            color: #fff;
            padding: 30px;
            font-size: 14px;
        }
        .email-footer a {
            color: #fff;
            margin: 0 10px;
        }
        .logo {
            width: 250px;
            margin-bottom: 20px;
        }
        .bold {
            font-weight: bold;
            font-size: 18px;
        }
        .social-icons img {
            width: 32px;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <img src="https://amttdetra.com/questionario/recursos/bandeira_ponta_grossa.png" alt="Logo" class="logo">
        <p class ="bold">Olá {{ $nome }},</p>
        <p class="bold">Recebemos seus dados.</p>
       <p class="bold">
            Seu cadastro foi validado! Dirija-se a qualquer um dos terminais para retirar o seu novo cartão.<br>
            <strong>Horários de atendimento:</strong><br>
            Terminal Central: Segunda a sexta, das 7h50 às 17h<br>
            Outros terminais: Segunda a sexta, das 7h às 13h e das 15h às 18h
        </p>
       
    </div>

    <div class="email-footer">
        <p>Entre em nossas redes sociais!</p>
        <div class="social-icons">
            <a href="https://www.instagram.com/prefspg" target="_blank">
                <img src="https://img.icons8.com/color/48/000000/instagram-new--v1.png"/>
            </a>
            <a href="https://www.facebook.com/prefspg" target="_blank">
                <img src="https://img.icons8.com/color/48/000000/facebook.png"/>
            </a>
        </div>
        <p style="margin-top: 15px;">Acesse-as para saber o que está sendo feito para o desenvolvimento de nossa cidade.</p>
    </div>
</body>
</html>
