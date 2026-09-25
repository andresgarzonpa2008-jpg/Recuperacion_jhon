<?php
declare(strict_types=1);
final class ClienteModelo {
  public function __construct(private PDO $pdo) {}
  public function listar(string $b='', int $pag=1, int $porPag=10): array {
    $off=($pag-1)*$porPag;
    $st=$this->pdo->prepare("SELECT * FROM clientes WHERE activo=1 AND (nombre LIKE :b1 OR documento LIKE :b2) ORDER BY nombre LIMIT :lim OFFSET :off");
    $st->bindValue(':b1','%'.$b.'%'); $st->bindValue(':b2','%'.$b.'%'); $st->bindValue(':lim',$porPag,PDO::PARAM_INT); $st->bindValue(':off',$off,PDO::PARAM_INT);
    $st->execute(); return $st->fetchAll();
  }
  public function contar(string $b=''): int {
    $st=$this->pdo->prepare("SELECT COUNT(*) c FROM clientes WHERE activo=1 AND (nombre LIKE :b1 OR documento LIKE :b2)");
    $st->execute([':b1'=>'%'.$b.'%',':b2'=>'%'.$b.'%']); return (int)$st->fetch()['c'];
  }
  public function crear(array $d): int {
    $st=$this->pdo->prepare("INSERT INTO clientes (nombre,documento,correo,telefono) VALUES (:n,:d,:c,:t)");
    $st->execute([':n'=>$d['nombre'],':d'=>$d['documento'],':c'=>$d['correo']?:null,':t'=>$d['telefono']?:null]);
    return (int)$this->pdo->lastInsertId();
  }
  public function actualizar(int $id, array $d): bool {
    $st=$this->pdo->prepare("UPDATE clientes SET nombre=:n,documento=:d,correo=:c,telefono=:t WHERE id=:id");
    return $st->execute([':n'=>$d['nombre'],':d'=>$d['documento'],':c'=>$d['correo']?:null,':t'=>$d['telefono']?:null,':id'=>$id]);
  }
  public function desactivar(int $id): bool {
    return $this->pdo->prepare("UPDATE clientes SET activo=0 WHERE id=:id")->execute([':id'=>$id]);
  }
  public function obtener(int $id): ?array {
    $st=$this->pdo->prepare("SELECT * FROM clientes WHERE id=:id"); $st->execute([':id'=>$id]);
    $r=$st->fetch(); return $r?:null;
  }
}
