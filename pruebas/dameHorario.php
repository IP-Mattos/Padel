<?php  


 error_reporting(E_ALL);
 ini_set('display_errors', '1');
 date_default_timezone_set("America/Montevideo");
 echo "cokie =" .$_COOKIE['goCookToken']."<br>";
    $token=$_COOKIE['goCookToken'];
    
?>
<hr>HORARIOS DAME HORARIOS DE UN SERVICIO
<form method="post" action="../accion/getHorarios.php">
    <input type="text" id="fecha" name="fecha" value="<?php echo(date("Y-m-d"));?>" placeholder="fecha">
    <input type="text" id ="servicio" name = "servicio" value="1" placeholder="servicio">
    <input type="text" id ="profe" name = "profe" value="1" placeholder="profe">
    <input type="submit" value="CONSULTAR HORARIOS">
</form>
    
<hr>HORARIOS DAME DIAS DE UN SERVICIO
<form method="post" action="../accion/getDias.php">
    
    <input type="text" id ="servicio" name = "servicio" value="1" placeholder="servicio">
    <input type="text" id ="profe" name = "profe" value="1" placeholder="profe">
    <input type="submit" value="CONSULTAR DIAS">
</form>

<hr>PERFIL - GUARDAR IMAGEN

<form method="post" action="../accion/saveImgPerfil.php" enctype="multipart/form-data">
    <input type="text" id ="idUser" name = "idUser" value="1" placeholder="id Usuario">
    <input  type="file" name="imgPerfilUser" id="imgPerfilUser" class="file-input__input" accept=".jpg,.jpeg,.png"/>
    <input type="submit" value="CARGAR IMAGEN">
</form>
<hr>PERFIL - GUARDAR PERFIL

<form method="post" action="../accion/savePerfil.php">
    <input type="text" id ="idUser" name = "idUser" value="1" placeholder="id Usuario">
    <input type="text" id ="nombre" name = "nombre" value="nombre" placeholder="nombre">
    <input type="text" id ="mail" name = "mail" value="mail" placeholder="mail">
    <input type="text" id ="usuario" name = "usuario" value="usuario" placeholder="usuario">
    <input type="text" id ="categoria" name = "categoria" value="4" placeholder="categoria">
    <input type="text" id ="cedula" name = "cedula" value="cedula" placeholder="cedula">
    <input type="text" id ="fechnac" name = "fechnac" value="1990-01-01" placeholder="fechnac">
    <input type="text" id ="frase" name = "frase" value="frase" placeholder="frase">
    <input type="text" id ="mascategorias" name = "mascategorias" value="2" placeholder="mascategorias">
    <input type="submit" value="GUARDAR CAMBIOS PERFIL">

</form>

<hr>PERFIL - MOSTRAR UN PERFIL DE OTRO USUARIO

<form method="post" action="../accion/getPerfil.php">
    <input type="text" id ="idPerfil" name = "idPerfil" value="1" placeholder="id de OTRO USUARIO">
    <input type="submit" value="VER PERFIL">

</form>


<hr>PERFIL - MOSTRAR TODOS LOS PERFILES CON FILTRO

<form method="post" action="../accion/getPerfiles.php">
    <input type="text" id ="filtroPerfil" name = "filtroPerfil" value="" placeholder="nombre apellido o nic">
    <input type="submit" value="VER PERFILES">

</form>

<hr>RESERVA - GUARDAR RESERVA
<form method="post" action="../accion/putReserv.php">

    <input type="text" id ="fecha" name = "fecha" value="2025/04/25" placeholder="fecha">
    <input type="text" id ="servicio" name = "servicio" value="1" placeholder="servicio">
    <input type="text" id ="usuario" name = "usuario" value="1" placeholder="usuario">
    <input type="text" id ="profe" name = "profe" value="0" placeholder="profe">
    <input type="text" id ="hora" name = "hora" value="10:00" placeholder="hora">
   
    <input type="submit" value="RESERVAR HORA">

</form>
<hr>RESERVA - CANCELAR RESERVA
<form method="post" action="../accion/putReservCancel.php">

    <input type="text" id ="idReserv" name = "idReserv" value="" placeholder="id de reserva">
   
   
    <input type="submit" value="ELIMINAR AHORA">

</form>
<hr>RESERVA - LISTAR MIS RESERVAS
<form method="post" action="../accion/getHorasUser.php">

    <input type="text" id ="fechaDesde" name = "fechaDesde" value="2025/04/01" placeholder="fechaDesde">
    <input type="text" id ="fechaHasta" name = "fechaHasta" value="2025/04/30" placeholder="fechaHasta">
    <input type="text" id ="idUser" name = "idUser" value="" placeholder="ID usuario">
   
    <input type="submit" value="LISTAR HORAS RESERVADAS USUARIO">

</form>

<hr>RESERVA - VS CONFIRMAR RESERVA SERVICIO 4
<form method="post" action="../accion/putConfirmVS.php">

    <input type="text" id ="idReserva" name = "idReserva" value="" placeholder="id de reserva">
    <input type="text" id ="idRival" name = "idRival" value="0" placeholder="el usuario actual">
     <input type="text" id ="mensaje" name = "mensaje" value="0" placeholder="si no te cagas jugamos">

    <input type="submit" value="CONFIRMAR VS">

</form>

<hr>RESERVA - AGREGAR INVITADOS
<form method="post" action="../accion/putConfirmInvitados.php">

    <input type="text" id ="idReserva" name = "idReserva" value="" placeholder="id de reserva">
    <input type="text" id ="idInvitado" name = "idInvitado" value="0" placeholder="el id de invitado">
    
    <input type="submit" value="CONFIRMAR INVITADO">

</form>

<hr>RESERVA - ELIMINAR UN INVITADO
<form method="post" action="../accion/putCancelInvitado.php">

    <input type="text" id ="idReserva" name = "idReserva" value="" placeholder="id de reserva">
    <input type="text" id ="idInvitado" name = "idInvitado" value="0" placeholder="el id de invitado a cancelar">
    
    <input type="submit" value="ELIMINAR INVITADO">

</form>

<hr>RESERVA - LISTAR RESERVAS (con mi usuario se de mis categorias)
<form method="post" action="../accion/getHsVs.php">
     <input type="text" id ="idUser" name = "idUser" value="" placeholder="id del usuario">
    <input type="text" id ="estado" name = "estado" value="0" placeholder="0 = Pendiente Sin Rival - 1 = ya tiene Rival">
   

    <input type="submit" value="LISTAR VS">

</form>
<div style="margin-top:50px;background-color:#060;">
    <hr>PUNTOS - CANJEAR PUNTOS
    <form method="post" action="../accion/putCanje.php">
        
        <input type="text" id ="puntos" name = "puntos" value="0" placeholder="Puntos a canjear">
    

        <input type="submit" value="SOLICITAR PUNTOS DE CANJE">

    </form>

    <hr>PUNTOS - CONFIRMAR CANJE DE PUNTOS
    <form method="post" action="../accion/putCanjeConfirm.php">
        
        <input type="text" id ="idCanje" name = "idCanje" value="0" placeholder="id del registro de canje">
    

        <input type="submit" value="CONFIRMAR  PUNTOS DE CANJE">

    </form>
     <hr>PUNTOS - CANCELAR CANJE DE PUNTOS
    <form method="post" action="../accion/putCanjeCancel.php">
        
        <input type="text" id ="idCanje" name = "idCanje" value="0" placeholder="id del registro de canje">
    

        <input type="submit" value="CANCELAR  PUNTOS DE CANJE">

    </form>
    <hr>PUNTOS - LISTAR CANJES PENDIENTES DE CONFIRMAR
    <form method="post" action="../accion/getCanjeUser.php">
    

        <input type="submit" value="LISTAR CANJES PENDIENTES DE CONFIRMAR">

    </form>

<br>

    </div>
<hr>PROFESORES - LISTAR PROFESORES
<form method="post" action="../accion/getProfesores.php">

   
   
    <input type="submit" value="LISTAR PROFESORES">

</form>



<hr>ADMINISTRADOR - LISTAR RESERVAS

<form method="post" action="../accion/getHorasReservAdmin.php">
    //dia actual 

    <input type="text" id ="fechaDesde" name = "fechaDesde" value="<?php echo(date("Y-m-d"));?>" placeholder="fechaDesde">
    <input type="text" id ="fechaHasta" name = "fechaHasta" value="<?php echo(date("Y-m-d"));?>" placeholder="fechaHasta">

   
    <input type="submit" value="LISTAR HORAS RESERVADAS USUARIO">

</form>

<hr>ADMINISTRADOR - CONFIRMAR RESERVA
<form method="post" action="../accion/putReservConfirm.php">

    <input type="text" id ="idReserv" name = "idReserv" value="" placeholder="id de reserva">
   
   
    <input type="submit" value="CONFIRMAR AHORA">

</form>

<hr>ADMINISTRADOR - RESTRINGIR HORAS DE CANCHA
<form method="post" action="../accion/putRestrictHoras.php">

    <input type="text" id ="fecha" name = "fecha" value="2025/04/25" placeholder="fecha">
    <input type="text" id ="servicio" name = "servicio" value="1" placeholder="servicio 1 = cancha 1 / servicio 6 = cancha 2">
    <input type="text" id ="usuario" name = "usuario" value="1" placeholder="usuario">
    <input type="text" id ="hora" name = "hora" value="10:00" placeholder="hora">
   
    <input type="submit" value="RESTRINGIR HORA">

</form>

<hr>ADMINISTRADOR - FDP - ESTABLECER MEDIOS DE PAGOS
<form method="post" action="../accion/putFDPAgenda.php">

    <input type="text" id ="idAgenda" name = "idAgenda" value="5" placeholder="id de agenda">
    <input type="text" id ="idusuario" name = "idUsuario" value="1" placeholder="id de usuario">
    <select name="fdpUsuario" id="fdpUsuario">
        <option value="EFECTIVO">EFECTIVO</option>
        <option value="TRANS">TRANS</option>
        <option value="MERCPAGO">MERCPAGO</option>
        <option value="DEBITO">DEBITO</option>
        <option value="CREDITO">CREDITO</option>
    </select>
    <input type="text" id ="idInvitado1" name = "idInvitado1" value="0" placeholder="id de invitado 1 - si no tiene va a ser cero">
    <select name="fdpInvitado1" id="fdpInvitado1">
        <option value="EFECTIVO">EFECTIVO</option>
        <option value="TRANS">TRANS</option>
        <option value="MERCPAGO">MERCPAGO</option>
        <option value="DEBITO">DEBITO</option>
        <option value="CREDITO">CREDITO</option>
    </select>
    <input type="text" id ="idInvitado2" name = "idInvitado2" value="0" placeholder="id de invitado 2 - si no tiene va a ser cero">
    <select name="fdpInvitado2" id="fdpInvitado2">
        <option value="EFECTIVO">EFECTIVO</option>
        <option value="TRANS">TRANS</option>
        <option value="MERCPAGO">MERCPAGO</option>
        <option value="DEBITO">DEBITO</option>
        <option value="CREDITO">CREDITO</option>
    </select>
    <input type="text" id ="idInvitado3" name = "idInvitado3" value="0" placeholder="id de invitado 3 - si no tiene va a ser cero">
    <select name="fdpInvitado3" id="fdpInvitado3">
        <option value="EFECTIVO">EFECTIVO</option>
        <option value="TRANS">TRANS</option>
        <option value="MERCPAGO">MERCPAGO</option>
        <option value="DEBITO">DEBITO</option>
        <option value="CREDITO">CREDITO</option>
    </select>
   <br>
    <label>MEDIO DE PAGOS</label>
    <input type="text" id ="impUsu" name = "impUsu" value="0" placeholder="importe pago por usuario">
    <input type="text" id ="impInv1" name = "impInv1" value="0" placeholder="importe pago por invitado 1">
    <input type="text" id ="impInv2" name = "impInv2" value="0" placeholder="importe pago por invitado 2">
    <input type="text" id ="impInv3" name = "impInv3" value="0" placeholder="importe pago por invitado 3">

    <input type="submit" value="GUARDAR PAGOS">

</form>

<hr>ADMINISTRADOR - LISTAR PAGOS

<form method="post" action="../accion/getFDP.php">
    //dia actual 

    <input type="text" id ="idAgenda" name = "idAgenda" value="0" placeholder="id Agenda">
   
    <input type="submit" value="LISTAR PAGOS">

</form>