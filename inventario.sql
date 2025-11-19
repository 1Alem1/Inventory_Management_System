-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 06:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventario`
--

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `IDItem` int(11) NOT NULL,
  `IDPedido` int(11) NOT NULL,
  `IDRepuesto` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movimientos`
--

CREATE TABLE `movimientos` (
  `IDMovimiento` int(11) NOT NULL,
  `IDRepuesto` int(11) NOT NULL,
  `Tipo` tinyint(4) NOT NULL,
  `Fecha` date NOT NULL,
  `IDUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pedidos`
--

CREATE TABLE `pedidos` (
  `IDPedido` int(11) NOT NULL,
  `IDUser` int(11) NOT NULL,
  `Fecha` date NOT NULL,
  `Estado` varchar(100) NOT NULL,
  `Observacion` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repuestos`
--

CREATE TABLE `repuestos` (
  `IDRepuesto` int(11) NOT NULL,
  `Nombre` varchar(60) NOT NULL,
  `Categoria` varchar(60) NOT NULL,
  `Descripcion` varchar(200) NOT NULL,
  `Stock` int(11) NOT NULL,
  `Precio` decimal(10,0) NOT NULL,
  `Imagen` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repuestos`
--

INSERT INTO `repuestos` (`IDRepuesto`, `Nombre`, `Categoria`, `Descripcion`, `Stock`, `Precio`, `Imagen`) VALUES
(1, 'Caño de cobre 1/4 25mts', 'Cañeria y Conexiones', 'Rollo de caños de cobre de 1/4 x 25mts', 50, 1250, 'https://tienda.demaco.ec/storage/product/13489/3218.png'),
(2, 'Caño de cobre 3/8 25mts', 'Cañeria y Conexiones', 'Rollo de caños de cobre 3/8 x 25mts', 50, 1500, 'https://tienda.demaco.ec/storage/product/13489/3218.png'),
(3, 'Caño de cobre 1/2 25mts', 'Cañeria y Conexiones', 'Rollo de caños de cobre 1/2 x 25mts', 50, 1600, 'https://tienda.demaco.ec/storage/product/13489/3218.png'),
(4, 'Caño de cobre 1/4 45mts', 'Cañeria y Conexiones', 'Rollo de caños de cobre 1/4 x 45mts', 50, 1750, 'https://i0.wp.com/lojamaqtec.com.br/wp-content/uploads/2022/09/image-removebg-preview-2022-09-26T105334.188.png?fit=500%2C500&ssl=1'),
(5, 'Caño de cobre 3/8 45mts', 'Cañeria y Conexiones', 'Rollo de caños de cobre 3/8 x 45mts', 50, 1850, 'https://i0.wp.com/lojamaqtec.com.br/wp-content/uploads/2022/09/image-removebg-preview-2022-09-26T105334.188.png?fit=500%2C500&ssl=1'),
(6, 'Caño de cobre 1/2 45mts', 'Cañeria y Conexiones', 'Rollo de caños de cobre 1/2 x 45mts', 50, 1900, 'https://i0.wp.com/lojamaqtec.com.br/wp-content/uploads/2022/09/image-removebg-preview-2022-09-26T105334.188.png?fit=500%2C500&ssl=1'),
(7, 'Aislación 1/4 2mts', 'Aislación térmica', 'Aislante térmico 1/4 2mts', 50, 700, 'https://www.grupomereti.com/cdn/shop/files/aislantecerrado_2.png?v=1693870161'),
(8, 'Aislación 3/8 2mts', 'Aislación térmica', 'Aislación 3/8 2mts', 50, 800, 'https://www.grupomereti.com/cdn/shop/files/aislantecerrado_2.png?v=1693870161'),
(9, 'Aislación 1/2 2mts', 'Aislación térmica', 'Aislación 1/2 2mts', 50, 900, 'https://www.grupomereti.com/cdn/shop/files/aislantecerrado_2.png?v=1693870161'),
(10, 'Cable eléctrico 40mts', 'Cables y Conectores', 'Rollo de cable eléctrico x 40mts', 50, 1200, 'https://ferrebaratilloimage.s3.us-east-2.amazonaws.com/105685.png'),
(11, 'Cable eléctrico 100mts', 'Cables y Conectores', 'Rollo de cable eléctrico x 100mts', 50, 1800, 'https://ferrebaratilloimage.s3.us-east-2.amazonaws.com/105685.png'),
(12, 'Precintos 100u Blancos', 'Sujeción', 'Bolsa de 100 precintos blancos', 50, 500, 'https://argseguridad.com/8958-medium_default/precintos-nylon-36mm-x-250mm-rocktool-x-100-unidades-blancos.jpg'),
(13, 'Precintos 100u Negros', 'Sujeción', 'Bolsa de 100 precintos negros', 50, 500, 'https://anilem.com.ar/images/product_image/2488/0?dpr=2.625&fit=contain&h=400&q=80&version=3a11c&w=400'),
(14, 'Cinta aisladora Blanca', 'Cintas', 'Cinta aisladora x 20mts Blanca', 50, 800, 'https://www.serviceitalia.com.ar/images/uploads/ecommerce/img17861.png'),
(15, 'Cinta aisladora Negra', 'Cintas', 'Cinta aisladora x 20mts Negra', 50, 800, 'https://www.serviceitalia.com.ar/images/uploads/ecommerce/img17863.png'),
(16, 'Cinta PVC Blanca', 'Cintas', 'Cinta PVC Blanca x 6mts', 50, 500, 'https://www.discamp.com/wp-content/uploads/2020/04/cinta-de-pvc-para-refrigeracion-300x300.png'),
(17, 'Cinta PVC Negra', 'Cintas', 'Cinta PVC Negra x 6mts', 50, 500, 'https://eqcgrupo.com/wp-content/uploads/2024/08/E700133.png'),
(18, 'Garrafa R22 5kg', 'Gases Refrigerantes', 'Garrafa refrigerante R22 x 5kg', 50, 1500, 'https://www.serviceitalia.com.ar/images/uploads/ecommerce/3965_0.png'),
(19, 'Garrafa R22 10kg', 'Gases Refrigerantes', 'Garrafa refrigerante R22 x 5kg', 50, 2500, 'https://www.serviceitalia.com.ar/images/uploads/ecommerce/img16061.png'),
(20, 'Garrafa R410 5kg', 'Gases Refrigerantes', 'Garrafa refrigerante R410 x 5kg', 50, 2700, 'https://www.serviceitalia.com.ar/images/uploads/ecommerce/img18150.png'),
(21, 'Garrafa R410 10kg', 'Gases Refrigerantes', 'Garrafa refrigerante R410 x 10kg', 50, 3600, 'https://www.serviceitalia.com.ar/images/uploads/ecommerce/2349_0.png'),
(22, 'Garrafa R32 5kg', 'Gases Refrigerantes', 'Garrafa refrigerante R32 x 5kg', 50, 2800, 'https://www.serviceitalia.com.ar/images/uploads/ecommerce/9024_0.webp'),
(23, 'Garrafa R32 10kg', 'Gases Refrigerantes', 'Garrafa refrigerante R32 x 10kg', 50, 3800, 'https://dcdn-us.mitiendanube.com/stores/006/232/189/products/10120134-photoroom-08a88bb86d9589d0df17604486567500-480-0.png'),
(24, 'Lata de soldar', 'Soldadura', 'Lata de soldar x unidad', 50, 1000, 'https://acdn-us.mitiendanube.com/stores/003/544/043/products/c187b242-8bb4-4e52-a58b-77ed00ae7791-3aa582f4224fd5402b17325538122527-640-0.png'),
(25, 'Varilla de plata', 'Soldadura', 'Varilla de plata x unidad', 50, 860, 'https://www.serviceitalia.com.ar/images/uploads/ecommerce/10420_0.png'),
(26, 'Tornillos 50u', 'Tornillería', 'Caja de tornillos 50 unidades', 50, 600, 'https://www.polytemp.com.ar/image/cache/catalog/Productos/TOOLISTER/PHOTO-2019-07-25-14-56-14[8]-550x550w.png');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `IDUser` int(11) NOT NULL,
  `Nombre` varchar(30) NOT NULL,
  `Email` varchar(80) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Rol` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`IDItem`),
  ADD KEY `ItemxPedido` (`IDPedido`),
  ADD KEY `ItemxRepuesto` (`IDRepuesto`);

--
-- Indexes for table `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`IDMovimiento`),
  ADD KEY `MovimientosxRepuesto` (`IDRepuesto`),
  ADD KEY `MovimientosxUsuario` (`IDUser`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`IDPedido`),
  ADD KEY `PedidosxUsuario` (`IDUser`);

--
-- Indexes for table `repuestos`
--
ALTER TABLE `repuestos`
  ADD PRIMARY KEY (`IDRepuesto`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`IDUser`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `IDItem` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `IDMovimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `IDPedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repuestos`
--
ALTER TABLE `repuestos`
  MODIFY `IDRepuesto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `IDUser` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `ItemxPedido` FOREIGN KEY (`IDPedido`) REFERENCES `pedidos` (`IDPedido`),
  ADD CONSTRAINT `ItemxRepuesto` FOREIGN KEY (`IDRepuesto`) REFERENCES `repuestos` (`IDRepuesto`);

--
-- Constraints for table `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `MovimientosxRepuesto` FOREIGN KEY (`IDRepuesto`) REFERENCES `repuestos` (`IDRepuesto`),
  ADD CONSTRAINT `MovimientosxUsuario` FOREIGN KEY (`IDUser`) REFERENCES `usuarios` (`IDUser`);

--
-- Constraints for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `PedidosxUsuario` FOREIGN KEY (`IDUser`) REFERENCES `usuarios` (`IDUser`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
