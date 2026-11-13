SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `username`, `phone`, `company`, `address`, `is_admin`, `status`, `approved_at`) VALUES
(1, 'Glo Shipa Admin', 'admin@gloshipa.com', NULL, '$2y$12$mkMXhPd3F0j0FlMihF3jgOvjX3DEFGoJZFQZcOBTOz8CDlozNk.VC', NULL, '2026-11-09 02:08:18', '2026-11-09 02:08:18', 'admin', NULL, NULL, NULL, 1, 'active', '2026-11-09 02:08:18'),
(2, 'Demo Client', 'demo@example.test', NULL, '$2y$12$EGAZN8D/EgU4qjNeXeVh1eZBqOYQp1HSvIm1GHNA1IqxQm2RXUjSO', NULL, '2026-11-09 02:08:19', '2026-11-09 02:08:19', 'demo', '+27 82 000 0000', 'Demo Imports (Pty) Ltd', NULL, 0, 'active', '2026-11-09 02:08:19'),
(3, 'Waiting For Approval', 'newclient@example.test', NULL, '$2y$12$NP7VTJvTkRX5gx.jSqj89OGQh4Zcs/NZ4L1PzZHcTSQgLZLIacH6e', NULL, '2026-11-09 02:08:19', '2026-11-09 03:50:43', 'newclient', NULL, 'Fresh Start Trading', NULL, 0, 'suspended', '2026-11-09 02:12:21'),
(4, 'Iveneko Consulting', 'concept254@gmail.com', NULL, '$2y$12$591hfk8CH0Dh.YtAdUQbc.05AB6c15Dx3jLzswX6/bhvHZWoTD0Bi', NULL, '2026-11-09 02:20:39', '2026-11-09 02:22:45', 'iveneko', '+27829588473', 'Bruno Paillard', NULL, 0, 'active', '2026-11-09 02:22:45'),
(5, 'James Magnus', 'kaychedder@gmail.com', NULL, '$2y$12$Tqk5q9Cug8SJcqAlgYHdfOP403Gxm4KOfTQV6WAMHJNcDSXchMGmu', NULL, '2026-11-09 03:47:59', '2026-11-09 03:47:59', 'james', '+27829586534', 'James Magnus co.', NULL, 0, 'pending', NULL),
(6, 'Hyper Cargo', 'hypercargo@yahoo.com', NULL, '$2y$12$oi.bC6peLxr08YLQNhk.IuTBwdd.rXudiVOMcddiUA2ELw4xElcr.', NULL, '2026-11-09 03:49:51', '2026-11-09 03:49:51', 'hypercargo', '+27683551203', 'Hyper Cargo', NULL, 0, 'pending', NULL);
INSERT INTO `shipments` (`id`, `tracking_id`, `container_number`, `user_id`, `origin`, `destination`, `current_location`, `cargo_description`, `weight_kg`, `status`, `eta`, `delivered_at`, `created_at`, `updated_at`) VALUES
(1, 'GSDEMO0001', 'MSKU1234567', 4, 'Shanghai, CN', 'Durban, ZA', 'Indian Ocean', 'Electronics', 12500, 'delayed', '2026-11-21 00:00:00', NULL, '2026-11-09 02:08:19', '2026-11-09 03:51:44'),
(2, 'GSDEMO0002', 'TCLU7654321', 4, 'Rotterdam, NL', 'Cape Town, ZA', 'Cape Town port', 'Auto parts', 14300, 'customs', '2026-11-15 00:00:00', NULL, '2026-11-09 02:08:19', '2026-11-09 02:26:13'),
(3, 'GSDEMO0003', 'HLCU1122334', 4, 'Dubai, AE', 'Johannesburg, ZA', 'Johannesburg', 'Textiles', 16100, 'delivered', '2026-11-09 00:00:00', '2026-11-07 02:08:19', '2026-11-09 02:08:19', '2026-11-09 03:28:56'),
(4, 'GSEJSOEXVCQP', 'MSKU00221', 4, 'Shangai CN', 'Durban ZA', 'Shangai CN', 'Computer spares', 1000, 'in_transit', '2026-11-09 00:00:00', NULL, '2026-11-09 02:16:02', '2026-11-09 02:24:32');
INSERT INTO `shipment_events` (`id`, `shipment_id`, `status`, `location`, `note`, `occurred_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'pending', 'Shanghai, CN', 'Shipment record created.', '2026-10-20 02:08:19', '2026-11-09 02:08:19', '2026-11-09 02:08:19'),
(2, 1, 'in_transit', 'At sea', 'Departed port of origin.', '2026-10-26 02:08:19', '2026-11-09 02:08:19', '2026-11-09 02:08:19'),
(3, 1, 'in_transit', 'Indian Ocean', NULL, '2026-11-06 02:08:19', '2026-11-09 02:08:19', '2026-11-09 02:08:19'),
(4, 2, 'pending', 'Rotterdam, NL', 'Shipment record created.', '2026-10-21 02:08:19', '2026-11-09 02:08:19', '2026-11-09 02:08:19'),
(5, 2, 'in_transit', 'At sea', 'Departed port of origin.', '2026-10-27 02:08:19', '2026-11-09 02:08:19', '2026-11-09 02:08:19'),
(6, 2, 'customs', 'Cape Town port', NULL, '2026-11-06 02:08:19', '2026-11-09 02:08:19', '2026-11-09 02:08:19'),
(7, 3, 'pending', 'Dubai, AE', 'Shipment record created.', '2026-10-22 02:08:19', '2026-11-09 02:08:19', '2026-11-09 02:08:19'),
(8, 3, 'in_transit', 'At sea', 'Departed port of origin.', '2026-10-28 02:08:19', '2026-11-09 02:08:19', '2026-11-09 02:08:19'),
(9, 3, 'delivered', 'Johannesburg', NULL, '2026-11-06 02:08:19', '2026-11-09 02:08:19', '2026-11-09 02:08:19'),
(10, 4, 'in_transit', 'Shangai CN', 'Shipment record created.', '2026-11-09 02:16:02', '2026-11-09 02:16:02', '2026-11-09 02:16:02'),
(11, 1, 'delayed', 'Indian Ocean', NULL, '2026-11-09 03:51:00', '2026-11-09 03:51:44', '2026-11-09 03:51:44');
SET FOREIGN_KEY_CHECKS=1;
