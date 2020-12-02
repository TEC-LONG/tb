INSERT INTO `exp`.`menu_permission_copy1`
(`post_date`, `menu__id`, `permission__id`, `route`, `request`, `navtab`, `user__id`, `update_time`, `display_name`, `parent_id`, `level3_href`, `level3_type`, `level`, `sort`)
VALUES 
(1585036262, 1, 1, '', 0, '', 0, 1585531602, '总后台管理', 0, '', 2, 1, 50),
(1585037184, 4, 2, '', 0, '', 0, 1585294272, '基础设置', 1, '', 2, 2, 50),
(1585037339, 4, 10, '/system/manage/menu/list', 1, 'system_manage_menuList', 0, 1585290174, '后台菜单管理', 2, '', 2, 3, 0),
(1585038608, 0, 4, '/system/manage/menu/add', 2, 'system_manage_menuList', 0, 1585289905, '添加后台菜单', 3, '', 2, 4, 0),
(1585038661, 0, 5, '/system/manage/menu/upd', 2, 'system_manage_menuList', 0, 1585289907, '编辑后台菜单', 3, '', 2, 4, 0),
(1585038764, 4, 10, '/system/manage/user/list', 1, 'system_manage_userList', 0, 1585290172, '后台管理员', 4, '', 2, 3, 0),
(1585038911, 0, 4, '/system/manage/user/edit', 1, 'system_manage_userEdit', 0, 1585289911, '添加后台管理员', 6, '', 2, 4, 0),
(1585038970, 0, 5, '/system/manage/user/edit', 1, 'system_manage_userEdit', 0, 1585289913, '编辑后台管理员', 6, '', 2, 4, 0),
(1585039028, 0, 7, '/system/manage/user/del', 1, 'system_manage_userList', 0, 1585289916, '删除后台管理员', 6, '', 2, 4, 0),
(1585039121, 4, 10, '/system/manage/user/group', 1, 'system_manage_userGroup', 0, 1585290169, '用户组管理', 2, '', 2, 3, 0),
(1585039193, 0, 4, '/system/manage/user/group/edit', 1, 'system_manage_userGroupEdit', 0, 1585289920, '添加用户组', 10, '', 2, 4, 0),
(1585039269, 0, 5, '/system/manage/user/group/edit', 1, 'system_manage_userGroupEdit', 0, 1585289922, '编辑用户组', 10, '', 2, 4, 0),
(1585039353, 0, 7, '/system/manage/user/group/del', 1, 'system_manage_userGroup', 0, 1585289925, '删除用户组', 10, '', 2, 4, 0),
(1585039451, 4, 10, '/system/manage/permission/menu', 1, 'system_manage_permissionMenu', 0, 1585290166, '菜单权限管理', 2, '', 2, 3, 0),
(1585039500, 0, 4, '/system/manage/permission/menu/edit', 1, 'system_manage_permissionMenuEdit', 0, 1585289930, '添加菜单权限', 14, '', 2, 4, 0),
(1585039557, 0, 5, '/system/manage/permission/menu/edit', 1, 'system_manage_permissionMenuEdit', 0, 1585289932, '编辑菜单权限', 14, '', 2, 4, 0),
(1585039619, 0, 7, '/system/manage/permission/menu/del', 1, 'system_manage_permissionMenu', 0, 1585289894, '删除菜单权限', 14, '', 2, 4, 0),
(1585039660, 4, 10, '/system/manage/permission/list', 1, 'system_manage_permissionList', 0, 1585290161, '权限管理列表', 2, '', 2, 3, 0),
(1585039712, 0, 4, '/system/manage/permission/edit', 1, 'system_manage_permissionEdit', 0, 1585289882, '添加权限', 18, '', 2, 4, 0),
(1585039767, 0, 5, '/system/manage/permission/edit', 0, 'system_manage_permissionEdit', 0, 1585289877, '编辑权限', 18, '', 2, 4, 0),
(1585039802, 0, 7, '/system/manage/permission/del', 1, 'system_manage_permissionList', 0, 1585289873, '删除权限', 18, '', 2, 4, 0);

INSERT INTO `exp`.`permission_copy1`
(`id`, `post_date`, `name`, `flag`, `user__id`, `update_time`)
VALUES
(1, 1585030759, '展示平台权限', 0, 0, 0),
(2, 1585030790, '展示二级菜单权限', 1, 0, 0),
(3, 1585030828, '查看列表权限', 2, 0, 0),
(4, 1585030852, '添加数据权限', 3, 0, 0),
(5, 1585030871, '编辑数据权限', 4, 0, 0),
(6, 1585030889, '数据搜索权限', 6, 0, 0),
(7, 1585030899, '删除数据权限', 5, 0, 0),
(8, 1585037645, '查看添加页权限', 7, 0, 0),
(9, 1585037666, '查看编辑页权限', 7, 0, 0),
(10, 1585120921, '展示三级菜单权限', 8, 0, 0);


INSERT INTO `exp`.`user_group_copy1`
(`id`, `post_date`, `comm`, `name`, `sort`)
VALUES
(1, 1584933185, '拥有无限的权限', '超级管理员组', 100),
(2, 1584933317, '', '工具管理员组', 0);


INSERT INTO `exp`.`user_group_permission_copy1`
(`post_date`, `menu_permission__id`, `user_group__id`)
VALUES
(1585304686, 1, 1),
(1585304686, 2, 1),
(1585304686, 3, 1),
(1585304686, 4, 1),
(1585304686, 5, 1),
(1585304686, 6, 1),
(1585304686, 7, 1),
(1585304686, 8, 1),
(1585304686, 9, 1),
(1585304686, 10, 1),
(1585304686, 11, 1),
(1585304686, 12, 1),
(1585304686, 13, 1),
(1585304686, 14, 1),
(1585304686, 15, 1),
(1585304686, 16, 1),
(1585304686, 17, 1),
(1585305738, 18, 1),
(1585305738, 19, 1),
(1585305738, 20, 1),
(1585305738, 21, 1);


