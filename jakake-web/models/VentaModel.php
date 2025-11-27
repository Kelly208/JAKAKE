<?php
namespace App\Models;

class VentaModel extends BaseModel {
    public function ventasPorPeriodo(string $desde, string $hasta): array {
        return $this->callProcedure("sp_ventas_periodo", [$desde, $hasta]);
    }

    public function registrarVenta(array $data): bool {
        return $this->callProcedure("sp_registrar_venta", [
            $data['cliente_id'],
            $data['producto_id'],
            $data['cantidad'],
            $data['total']
        ]);
    }
}
