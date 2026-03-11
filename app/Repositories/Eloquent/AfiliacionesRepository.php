<?php
  
  namespace App\Repositories\Eloquent;

  use App\Models\Afiliacion;
  use App\Repositories\Interfaces\AfiliacionInterface;

  class AfiliacionesRepository implements AfiliacionInterface 
  {
      public function getAllAfiliaciones()
      {
          $afiliaciones = Afiliacion::all();
          return $afiliaciones;
      }

      public function getAfiliacionById($id)
      {
          $afiliacion = Afiliacion::find($id);
          return $afiliacion;
      }

      public function createAfiliacion(array $data)
      {
          $afiliacion = Afiliacion::create($data);
          return $afiliacion;
      }

      public function updateAfiliacion($id, array $data)
      {
          $afiliacion = Afiliacion::find($id);
          if (!$afiliacion) {
              return null;
          }

          $afiliacion->update($data);
          return $afiliacion;
      }

      public function deleteAfiliacion($id)
      {
          $afiliacion = Afiliacion::find($id);
          if (!$afiliacion) {
              return false;
          }

          $afiliacion->delete();
          return true;
      }
  } 