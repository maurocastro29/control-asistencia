-- Permisos de los módulos Motivos de inactividad e Inactividad.
-- Compatible con MySQL/MariaDB. INSERT IGNORE evita duplicados por
-- la restricción única de (name, guard_name) sin modificar registros existentes.

INSERT IGNORE INTO `permissions`
    (`name`, `display_name`, `guard_name`, `is_active`, `created_at`, `updated_at`)
VALUES
    ('inactivity-reasons.view',   'Motivos de inactividad: Consultar', 'web', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('inactivity-reasons.create', 'Motivos de inactividad: Crear',    'web', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('inactivity-reasons.edit',   'Motivos de inactividad: Editar',   'web', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('inactivity-reasons.delete', 'Motivos de inactividad: Eliminar', 'web', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('inactivity-days.view',      'Días de inactividad: Consultar',    'web', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('inactivity-days.create',    'Días de inactividad: Crear',       'web', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('inactivity-days.edit',      'Días de inactividad: Editar',      'web', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('inactivity-days.delete',    'Días de inactividad: Eliminar',    'web', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
