<?php
namespace App\Models;

class ReporteModel extends BaseModel {
    public function bonosActivos(): array {
        return $this->query("SELECT * FROM vw_bonos_activos");
    }

    public function auditoriaReciente(): array {
        return $this->query("SELECT * FROM vw_auditoria_reciente");
    }
}
