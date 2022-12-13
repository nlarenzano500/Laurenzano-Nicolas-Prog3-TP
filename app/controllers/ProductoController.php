<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController implements IApiUsable {

    public function CargarUno($request, $response, $args) {
        Logger::Log($request, "carga producto");

        $parametros = $request->getParsedBody();

        $descripcion = $parametros['descripcion'];
        $precio = $parametros['precio'];
        $sector = $parametros['sector'];

        // Creamos el producto
        $producto = new Producto();
        $producto->descripcion = $descripcion;
        $producto->precio = $precio;
        $producto->sector = $sector;
        $producto->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado."));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args) {
        Logger::Log($request, "consulta producto");

        // Buscamos producto por id
        $id = $args['id'];

        $producto = Producto::obtenerProducto($id);
        if ($producto) {
            $payload = json_encode($producto);
        } else {
            $payload = json_encode(array("mensaje" => "No se encontró el producto."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args) {
        Logger::Log($request, "consulta productos");

        $lista = Producto::obtenerTodos();
        $payload = json_encode($lista);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args) {
        Logger::Log($request, "mod producto");

        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $descripcion = $parametros['descripcion'];
        $precio = $parametros['precio'];
        $sector = $parametros['sector'];
        $vendidos = $parametros['vendidos'];

        // Creamos el producto
        $prod = new Producto();
        $prod->descripcion = $descripcion;
        $prod->precio = $precio;
        $prod->sector = $sector;
        $prod->vendidos = $vendidos;

        if ($prod->modificarProducto($id)) {
            $payload = json_encode(array("mensaje" => "Producto modificado."));
        } else {
            $payload = json_encode(array("mensaje" => "El producto no fue modificado."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args) {
        Logger::Log($request, "elimina producto");

        $parametros = $request->getParsedBody();
        $id = $parametros['id'];

        if (Producto::borrarProducto($id)) {
            $payload = json_encode(array("mensaje" => "Producto eliminado."));
        } else {
            $payload = json_encode(array("mensaje" => "El producto no fue eliminado."));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
