# editar_cita.py
import mysql.connector
from flask import Blueprint, render_template, request, redirect, url_for, flash, session
from authentication import login_required
from db import connect_to_db

editar_cita_routes = Blueprint('editar_cita', __name__)

# Ruta para editar una cita
@editar_cita_routes.route('/editar_cita/<int:cita_id>', methods=['GET', 'POST'])
@login_required
def editar_cita(cita_id):
    # Redirigir al usuario a la página de edición de cita
    return render_template('editar_cita.html', cita_id=cita_id)
def editar_cita(cita_id):
    # Conectarse a la base de datos
    conn = connect_to_db()
    if not conn:
        flash("Error de conexión a la base de datos", "error")
        return redirect(url_for('admin.admin'))
    
    if request.method == 'POST':
        # Obtener los datos del formulario de edición
        nueva_fecha = request.form['nueva_fecha']
        nueva_hora = request.form['nueva_hora']
        nuevo_id_servicio = int(request.form.get('nuevo_servicio')) if request.form.get('nuevo_servicio') else None
        nueva_disponibilidad = request.form.get('nueva_disponibilidad') == 'True'

        try:
            cursor = conn.cursor()
            
            # Actualizar la cita en la base de datos
            sql_update_cita = "UPDATE Horarios SET id_servicio = %s, fecha = %s, hora = %s, disponibilidad = %s WHERE id_horario = %s"
            values = (nuevo_id_servicio, nueva_fecha, nueva_hora, nueva_disponibilidad, cita_id)
            cursor.execute(sql_update_cita, values)
            conn.commit()

            flash("Cita actualizada correctamente", "success")
        except mysql.connector.Error as err:
            flash(f"Error en la base de datos: {err}", "error")
        finally:
            cursor.close()
            conn.close()

        return redirect(url_for('admin.admin'))
    
    # Obtener la cita a editar desde la base de datos
    try:
        cursor = conn.cursor()
        sql_get_cita = "SELECT Horarios.*, Servicios.nombre AS nombre_servicio FROM Horarios INNER JOIN Servicios ON Horarios.id_servicio = Servicios.id_servicio WHERE Horarios.id_horario = %s"
        cursor.execute(sql_get_cita, (cita_id,))
        cita = cursor.fetchone()
        cursor.close()
    except mysql.connector.Error as err:
        flash(f"Error al obtener la cita: {err}", "error")
        cita = None
    
    if not cita:
        flash("La cita no existe", "error")
        return redirect(url_for('admin.admin'))
    
    # Obtener los servicios desde la base de datos
    try:
        cursor = conn.cursor()
        sql_get_servicios = "SELECT * FROM Servicios"
        cursor.execute(sql_get_servicios)
        servicios = cursor.fetchall()
        cursor.close()
    except mysql.connector.Error as err:
        flash(f"Error al consultar los servicios: {err}", "error")
        servicios = []

    return render_template('editar_cita.html', cita=cita, servicios=servicios)
