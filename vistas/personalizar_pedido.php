<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Personaliza tu pastel</title>
    <link rel="stylesheet" href="../css/personalizado.css">
    <script>
        function toggleTransferenciaInfo() {
            const metodo = document.getElementById("metodo_pago").value;
            document.getElementById("info_transferencia").style.display = metodo === "transferencia" ? "block" : "none";
        }
    </script>
</head>

<body>
    <a href="../index.php" class="btn-volver-inicio">← Volver al inicio</a>
    <h2>Personaliza tu pastel</h2>
    <form action="../procesar_personalizado.php" method="POST">
        <label>Tamaño:</label>
<select name="tamano" required onchange="actualizarPrecio()">
    <option value="Chico">Chico (8 porciones)</option>
    <option value="Mediano">Mediano (12 porciones)</option>
    <option value="Grande">Grande (16+ porciones)</option>
    <option value="Extra Grande">Extra Grande (25+ porciones)</option>
</select>

<p><strong>Total del pastel:</strong> <span id="precio_pastel">$300</span></p>


        <label>Sabor:</label>
        <select name="sabor" required>
            <option value="Chocolate">Chocolate</option>
            <option value="Vainilla">Vainilla</option>
            <option value="Tres Leches">Tres Leches</option>
            <option value="Red Velvet">Red Velvet</option>
            <option value="Zanahoria">Zanahoria</option>
            <option value="Choco-Vainilla">Choco-Vainilla</option>
            <option value="Moka">Moka</option>
            <option value="Limón">Limón</option>
            <option value="Marble">Marble</option>
            <option value="Coco">Coco</option>
            <option value="Queso Crema">Queso Crema</option>
        </select>

        <label>Relleno:</label>
        <select name="relleno" required>
            <option value="Fresa">Fresa</option>
            <option value="Cajeta">Cajeta</option>
            <option value="Nutella">Nutella</option>
            <option value="Durazno">Durazno</option>
            <option value="Zarzamora">Zarzamora</option>
            <option value="Queso Crema">Queso Crema</option>
            <option value="Piña">Piña</option>
            <option value="Frutas Mixtas">Frutas Mixtas</option>
            <option value="Merengue">Merengue</option>
            <option value="Chantilly">Chantilly</option>
            <option value="Ganache de Chocolate">Ganache de Chocolate</option>
        </select>

        <label>Tipo de cobertura:</label>
        <select name="cobertura" required>
            <option value="Fondant">Fondant</option>
            <option value="Chantilly">Chantilly</option>
            <option value="Ganache">Ganache</option>
            <option value="Buttercream">Buttercream</option>
            <option value="Glaseado Simple">Glaseado Simple</option>
        </select>

        <label>Decoración/Toppings:</label>
        <select name="topping" required>
            <option value="Frutas naturales">Frutas naturales</option>
            <option value="Chispas de chocolate">Chispas de chocolate</option>
            <option value="Confites">Confites</option>
            <option value="Nueces">Nueces</option>
            <option value="Flores comestibles">Flores comestibles</option>
            <option value="Figuras temáticas">Figuras temáticas</option>
        </select>

        <label>Forma del pastel:</label>
        <select name="forma" required>
            <option value="Redondo">Redondo</option>
            <option value="Cuadrado">Cuadrado</option>
            <option value="Corazón">Corazón</option>
            <option value="Rectangular">Rectangular</option>
            <option value="Forma personalizada">Forma personalizada</option>
        </select>

        <label>Tema o color:</label>
        <input type="text" name="tema" placeholder="Ej. Unicornio, Rosa pastel" required>

        <label>Dedicatoria:</label>
        <input type="text" name="dedicatoria" maxlength="100" placeholder="Feliz cumpleaños, Ana" required>

        <label>Fecha de entrega:</label>
        <input type="date" name="fecha_entrega" required>

        <label>Hora de entrega:</label>
        <input type="time" name="hora_entrega" min="09:00" max="18:00" required>

        <label>Método de pago:</label>
        <select name="metodo_pago" id="metodo_pago" onchange="toggleTransferenciaInfo()" required>
            <option value="efectivo">Efectivo al recoger</option>
            <option value="transferencia">Transferencia</option>
        </select>

        <div id="info_transferencia" style="display:none; background:#f1f1f1; padding:10px; margin-bottom:10px;">
            <strong>Transferencia:</strong><br>
            Banco: BBVA<br>
            Cuenta: 4152 5678 9012 3456<br>
            A nombre de: Pastelería El Ángel<br>
            <em>Recuerda mostrar tu comprobante al recoger.</em>
        </div>

        <label>Observaciones:</label><br>
        <textarea name="observaciones" rows="3" cols="40" placeholder="Opcional..."></textarea><br><br>

        <button type="button" class="btn-confirmar" onclick="abrirModal()">Confirmar pedido personalizado</button>
        <!-- Modal de confirmación -->
        <div id="modalConfirmacion" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); z-index:999;">
            <div style="background:#fff; max-width:400px; margin:100px auto; padding:30px; border-radius:10px; text-align:center;">
                <h3>¿Estás seguro de confirmar tu pedido personalizado?</h3>
                <p>Una vez confirmado, se registrará en el sistema.</p>
                <button onclick="document.getElementById('formPersonalizado').submit()" style="background-color:#bf5e7a; color:#fff; border:none; padding:10px 20px; margin-top:10px; border-radius:8px;">Sí, confirmar</button>
                <button onclick="cerrarModal()" style="background-color:#ccc; border:none; padding:10px 20px; margin-left:10px; border-radius:8px;">Cancelar</button>
            </div>
        </div>
        <!-- Al final del <form> pero antes del modal -->
<input type="hidden" name="precio" id="precio_input" value="300">


    </form>

 


    <p class="mensaje-contacto" style="text-align:center; margin-top:20px;">
        ¿Requieres un diseño más específico o temático?<br>
        Comunícate con nosotros al <strong>477 589 4366</strong> para atención personalizada.
    </p>
    <script>
        function abrirModal() {
            document.getElementById("modalConfirmacion").style.display = "block";
        }

        function cerrarModal() {
            document.getElementById("modalConfirmacion").style.display = "none";
        }
    </script>
    <script>
        function actualizarPrecio() {
            const tamaño = document.querySelector('select[name="tamano"]').value;
            let precio = 300;

            switch (tamaño) {
                case 'Mediano':
                    precio = 450;
                    break;
                case 'Grande':
                    precio = 550;
                    break;
                case 'Extra Grande':
                    precio = 650;
                    break;
                default:
                    precio = 300;
            }

            document.getElementById("precio_pastel").textContent = "$" + precio;
            document.getElementById("precio_input").value = precio;
        }

        document.querySelector('select[name="tamano"]').addEventListener('change', actualizarPrecio);
        window.onload = actualizarPrecio;
    </script>

</body>

</html>