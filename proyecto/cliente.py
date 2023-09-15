# cliente.py
from flask import Blueprint, render_template , session
from authentication import login_required
from db import connect_to_db

cliente_routes = Blueprint('cliente', __name__)

@cliente_routes.route('/cliente')
@login_required
def cliente():
    user_id = session['user_id']
    conn = connect_to_db()
    if conn:
        cursor = conn.cursor()
        sql = "SELECT c.id_cita, s.nombre, h.fecha, h.hora, h.disponibilidad FROM Citas c JOIN Horarios h ON c.id_horario = h.id_horario JOIN Servicios s ON h.id_servicio = s.id_servicio WHERE c.id_usuario = %s"
        cursor.execute(sql, (user_id,))
        citas = cursor.fetchall()
        cursor.close()
        conn.close()
        return render_template('cliente.html', citas=citas)
    else:
        return "Error de conexión a la base de datos"
