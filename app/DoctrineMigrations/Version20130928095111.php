<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130928095111 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE page__block DROP FOREIGN KEY FK_66F58DA0727ACA70");
        $this->addSql("ALTER TABLE page__block DROP FOREIGN KEY FK_66F58DA0C4663E4");
        $this->addSql("ALTER TABLE page__page DROP FOREIGN KEY FK_2FAE39ED158E0B66");
        $this->addSql("ALTER TABLE page__page DROP FOREIGN KEY FK_2FAE39ED727ACA70");
        $this->addSql("ALTER TABLE page__snapshot DROP FOREIGN KEY FK_3963EF9AC4663E4");
        $this->addSql("ALTER TABLE page__page DROP FOREIGN KEY FK_2FAE39EDF6BD1646");
        $this->addSql("ALTER TABLE page__snapshot DROP FOREIGN KEY FK_3963EF9AF6BD1646");
        $this->addSql("ALTER TABLE phpcr_nodes_references DROP FOREIGN KEY FK_F3BF7E1953C1C61");
        $this->addSql("ALTER TABLE phpcr_nodes_weakreferences DROP FOREIGN KEY FK_F0E4F6FA158E0B66");
        $this->addSql("ALTER TABLE phpcr_nodes_weakreferences DROP FOREIGN KEY FK_F0E4F6FA953C1C61");
        $this->addSql("DROP TABLE ext_log_entries");
        $this->addSql("DROP TABLE ext_translations");
        $this->addSql("DROP TABLE kuma_acl_changesets");
        $this->addSql("DROP TABLE kuma_checkbox_page_parts");
        $this->addSql("DROP TABLE kuma_choice_page_parts");
        $this->addSql("DROP TABLE kuma_dashboard_configurations");
        $this->addSql("DROP TABLE kuma_download_page_parts");
        $this->addSql("DROP TABLE kuma_email_page_parts");
        $this->addSql("DROP TABLE kuma_file_upload_page_parts");
        $this->addSql("DROP TABLE kuma_folders");
        $this->addSql("DROP TABLE kuma_form_submission_fields");
        $this->addSql("DROP TABLE kuma_form_submissions");
        $this->addSql("DROP TABLE kuma_groups");
        $this->addSql("DROP TABLE kuma_groups_roles");
        $this->addSql("DROP TABLE kuma_header_page_parts");
        $this->addSql("DROP TABLE kuma_image_page_parts");
        $this->addSql("DROP TABLE kuma_line_page_parts");
        $this->addSql("DROP TABLE kuma_link_page_parts");
        $this->addSql("DROP TABLE kuma_media");
        $this->addSql("DROP TABLE kuma_multi_line_text_page_parts");
        $this->addSql("DROP TABLE kuma_node_queued_node_translation_actions");
        $this->addSql("DROP TABLE kuma_node_translations");
        $this->addSql("DROP TABLE kuma_node_versions");
        $this->addSql("DROP TABLE kuma_nodes");
        $this->addSql("DROP TABLE kuma_page_part_refs");
        $this->addSql("DROP TABLE kuma_page_template_configuration");
        $this->addSql("DROP TABLE kuma_raw_html_page_parts");
        $this->addSql("DROP TABLE kuma_roles");
        $this->addSql("DROP TABLE kuma_seo");
        $this->addSql("DROP TABLE kuma_single_line_text_page_parts");
        $this->addSql("DROP TABLE kuma_sitemap_pages");
        $this->addSql("DROP TABLE kuma_slide_page_parts");
        $this->addSql("DROP TABLE kuma_submit_button_page_parts");
        $this->addSql("DROP TABLE kuma_text_page_parts");
        $this->addSql("DROP TABLE kuma_to_top_page_parts");
        $this->addSql("DROP TABLE kuma_toc_page_parts");
        $this->addSql("DROP TABLE kuma_users");
        $this->addSql("DROP TABLE kuma_users_groups");
        $this->addSql("DROP TABLE kuma_video_page_parts");
        $this->addSql("DROP TABLE page__block");
        $this->addSql("DROP TABLE page__block_audit");
        $this->addSql("DROP TABLE page__page");
        $this->addSql("DROP TABLE page__page_audit");
        $this->addSql("DROP TABLE page__site");
        $this->addSql("DROP TABLE page__site_audit");
        $this->addSql("DROP TABLE page__snapshot");
        $this->addSql("DROP TABLE page__snapshot_audit");
        $this->addSql("DROP TABLE phpcr_binarydata");
        $this->addSql("DROP TABLE phpcr_internal_index_types");
        $this->addSql("DROP TABLE phpcr_namespaces");
        $this->addSql("DROP TABLE phpcr_nodes");
        $this->addSql("DROP TABLE phpcr_nodes_references");
        $this->addSql("DROP TABLE phpcr_nodes_weakreferences");
        $this->addSql("DROP TABLE phpcr_type_childs");
        $this->addSql("DROP TABLE phpcr_type_nodes");
        $this->addSql("DROP TABLE phpcr_type_props");
        $this->addSql("DROP TABLE phpcr_workspaces");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), INDEX translations_lookup_idx (locale, object_class, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_acl_changesets (id BIGINT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ref_id BIGINT NOT NULL, ref_entity_name VARCHAR(255) NOT NULL, changeset LONGTEXT NOT NULL COMMENT '(DC2Type:array)', pid INT DEFAULT NULL, status INT NOT NULL, created DATETIME DEFAULT NULL, last_modified DATETIME DEFAULT NULL, INDEX IDX_953E7491A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_checkbox_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, required TINYINT(1) DEFAULT NULL, error_message_required VARCHAR(255) DEFAULT NULL, `label` VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_choice_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, expanded TINYINT(1) DEFAULT NULL, multiple TINYINT(1) DEFAULT NULL, choices LONGTEXT DEFAULT NULL, empty_value VARCHAR(255) DEFAULT NULL, required TINYINT(1) DEFAULT NULL, error_message_required VARCHAR(255) DEFAULT NULL, `label` VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_dashboard_configurations (id BIGINT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_download_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, media_id BIGINT DEFAULT NULL, INDEX IDX_65E8DB81EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_email_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, required TINYINT(1) DEFAULT NULL, error_message_required VARCHAR(255) DEFAULT NULL, error_message_invalid VARCHAR(255) DEFAULT NULL, `label` VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_file_upload_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, required TINYINT(1) DEFAULT NULL, error_message_required VARCHAR(255) DEFAULT NULL, `label` VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_folders (id BIGINT AUTO_INCREMENT NOT NULL, parent_id BIGINT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, rel VARCHAR(255) DEFAULT NULL, internal_name VARCHAR(255) DEFAULT NULL, deleted TINYINT(1) NOT NULL, INDEX IDX_2D3C07E4727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_form_submission_fields (id BIGINT AUTO_INCREMENT NOT NULL, form_submission_id BIGINT DEFAULT NULL, fieldName VARCHAR(255) NOT NULL, `label` VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, sfsf_value VARCHAR(255) DEFAULT NULL, tfsf_value LONGTEXT DEFAULT NULL, bfsf_value TINYINT(1) DEFAULT NULL, cfsf_value LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', expanded TINYINT(1) DEFAULT NULL, multiple TINYINT(1) DEFAULT NULL, choices LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', required TINYINT(1) DEFAULT NULL, ffsf_value VARCHAR(255) DEFAULT NULL, efsf_value VARCHAR(255) DEFAULT NULL, INDEX IDX_A0B2BD32422B0E0C (form_submission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_form_submissions (id BIGINT AUTO_INCREMENT NOT NULL, node_id BIGINT DEFAULT NULL, ip_address VARCHAR(255) NOT NULL, lang VARCHAR(255) NOT NULL, created DATETIME NOT NULL, INDEX IDX_6493A44D460D9FD7 (node_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_groups_roles (group_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_B7EFE85EFE54D947 (group_id), INDEX IDX_B7EFE85ED60322AC (role_id), PRIMARY KEY(group_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_header_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, niv INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_image_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, media_id BIGINT DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, open_in_new_window TINYINT(1) DEFAULT NULL, alt_text VARCHAR(255) DEFAULT NULL, INDEX IDX_CF8612B5EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_line_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_link_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL, openinnewwindow TINYINT(1) DEFAULT NULL, text VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_media (id BIGINT AUTO_INCREMENT NOT NULL, folder_id BIGINT DEFAULT NULL, uuid VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, content_type VARCHAR(255) NOT NULL, metadata LONGTEXT NOT NULL COMMENT '(DC2Type:array)', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, filesize INT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, deleted TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_47400F63D17F50A6 (uuid), INDEX IDX_47400F63162CB942 (folder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_multi_line_text_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, required TINYINT(1) DEFAULT NULL, error_message_required VARCHAR(255) DEFAULT NULL, regex VARCHAR(255) DEFAULT NULL, error_message_regex VARCHAR(255) DEFAULT NULL, `label` VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_node_queued_node_translation_actions (id BIGINT AUTO_INCREMENT NOT NULL, node_translation_id BIGINT DEFAULT NULL, action VARCHAR(255) NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_D270D8D1E0B87CE0 (node_translation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_node_translations (id BIGINT AUTO_INCREMENT NOT NULL, node_id BIGINT DEFAULT NULL, public_node_version_id BIGINT DEFAULT NULL, lang VARCHAR(255) NOT NULL, online TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, weight SMALLINT DEFAULT NULL, UNIQUE INDEX ix_kuma_node_translations_node_lang (node_id, lang), INDEX IDX_5E8968CD460D9FD7 (node_id), INDEX IDX_5E8968CDB9A563EE (public_node_version_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_node_versions (id BIGINT AUTO_INCREMENT NOT NULL, node_translation_id BIGINT DEFAULT NULL, origin_id BIGINT DEFAULT NULL, type VARCHAR(255) NOT NULL, owner VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, ref_id BIGINT NOT NULL, ref_entity_name VARCHAR(255) NOT NULL, INDEX IDX_FF496637E0B87CE0 (node_translation_id), INDEX IDX_FF49663756A273CC (origin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_nodes (id BIGINT AUTO_INCREMENT NOT NULL, parent_id BIGINT DEFAULT NULL, sequence_number INT NOT NULL, lft INT DEFAULT NULL, lvl INT DEFAULT NULL, rgt INT DEFAULT NULL, deleted TINYINT(1) NOT NULL, hidden_from_nav TINYINT(1) NOT NULL, ref_entity_name VARCHAR(255) NOT NULL, internal_name VARCHAR(255) DEFAULT NULL, INDEX IDX_3051AB93727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_page_part_refs (id BIGINT AUTO_INCREMENT NOT NULL, pageId BIGINT NOT NULL, pageEntityname VARCHAR(255) NOT NULL, context VARCHAR(255) NOT NULL, sequencenumber INT NOT NULL, pagePartId BIGINT NOT NULL, pagePartEntityname VARCHAR(255) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_page_template_configuration (id BIGINT AUTO_INCREMENT NOT NULL, page_id BIGINT NOT NULL, page_entity_name VARCHAR(255) NOT NULL, page_template VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_raw_html_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, content LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_roles (id INT AUTO_INCREMENT NOT NULL, role VARCHAR(70) NOT NULL, UNIQUE INDEX UNIQ_9B5280A857698A6A (role), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_seo (id BIGINT AUTO_INCREMENT NOT NULL, og_image_id BIGINT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description LONGTEXT DEFAULT NULL, meta_author VARCHAR(255) DEFAULT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, meta_robots VARCHAR(255) DEFAULT NULL, meta_revised VARCHAR(255) DEFAULT NULL, og_type VARCHAR(255) DEFAULT NULL, og_title VARCHAR(255) DEFAULT NULL, og_description LONGTEXT DEFAULT NULL, extra_metadata LONGTEXT DEFAULT NULL, cim_keyword VARCHAR(24) DEFAULT NULL, ref_id BIGINT NOT NULL, ref_entity_name VARCHAR(255) NOT NULL, linked_in_recommend_link VARCHAR(255) DEFAULT NULL, linked_in_recommend_product_id VARCHAR(255) DEFAULT NULL, og_url VARCHAR(255) DEFAULT NULL, INDEX IDX_4695F4A76EFCB8B8 (og_image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_single_line_text_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, required TINYINT(1) DEFAULT NULL, error_message_required VARCHAR(255) DEFAULT NULL, regex VARCHAR(255) DEFAULT NULL, error_message_regex VARCHAR(255) DEFAULT NULL, `label` VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_sitemap_pages (id BIGINT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, page_title VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_slide_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, media_id BIGINT DEFAULT NULL, INDEX IDX_EA7A3F17EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_submit_button_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, `label` VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_text_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, content LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_to_top_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_toc_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_39EF0B8692FC23A8 (username_canonical), UNIQUE INDEX UNIQ_39EF0B86A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_users_groups (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_AFF816DDA76ED395 (user_id), INDEX IDX_AFF816DDFE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE kuma_video_page_parts (id BIGINT AUTO_INCREMENT NOT NULL, media_id BIGINT DEFAULT NULL, INDEX IDX_23FF1D35EA9FDD75 (media_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page__block (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, page_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, type VARCHAR(64) NOT NULL, settings LONGTEXT NOT NULL COMMENT '(DC2Type:json)', enabled TINYINT(1) DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_66F58DA0727ACA70 (parent_id), INDEX IDX_66F58DA0C4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page__block_audit (id INT NOT NULL, rev INT NOT NULL, parent_id INT DEFAULT NULL, page_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, type VARCHAR(64) DEFAULT NULL, settings LONGTEXT DEFAULT NULL COMMENT '(DC2Type:json)', enabled TINYINT(1) DEFAULT NULL, position INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, revtype VARCHAR(4) NOT NULL, PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page__page (id INT AUTO_INCREMENT NOT NULL, target_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, site_id INT DEFAULT NULL, route_name VARCHAR(255) NOT NULL, page_alias VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, decorate TINYINT(1) NOT NULL, edited TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, slug LONGTEXT DEFAULT NULL, url LONGTEXT DEFAULT NULL, custom_url LONGTEXT DEFAULT NULL, request_method VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, meta_keyword VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, javascript LONGTEXT DEFAULT NULL, stylesheet LONGTEXT DEFAULT NULL, raw_headers LONGTEXT DEFAULT NULL, template VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2FAE39EDF6BD1646 (site_id), INDEX IDX_2FAE39ED727ACA70 (parent_id), INDEX IDX_2FAE39ED158E0B66 (target_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page__page_audit (id INT NOT NULL, rev INT NOT NULL, site_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, target_id INT DEFAULT NULL, route_name VARCHAR(255) DEFAULT NULL, page_alias VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, decorate TINYINT(1) DEFAULT NULL, edited TINYINT(1) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, slug LONGTEXT DEFAULT NULL, url LONGTEXT DEFAULT NULL, custom_url LONGTEXT DEFAULT NULL, request_method VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, meta_keyword VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, javascript LONGTEXT DEFAULT NULL, stylesheet LONGTEXT DEFAULT NULL, raw_headers LONGTEXT DEFAULT NULL, template VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, revtype VARCHAR(4) NOT NULL, PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page__site (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, relative_path VARCHAR(255) DEFAULT NULL, host VARCHAR(255) NOT NULL, enabled_from DATETIME NOT NULL, enabled_to DATETIME NOT NULL, is_default TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, locale VARCHAR(6) DEFAULT NULL, title VARCHAR(64) DEFAULT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page__site_audit (id INT NOT NULL, rev INT NOT NULL, enabled TINYINT(1) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, relative_path VARCHAR(255) DEFAULT NULL, host VARCHAR(255) DEFAULT NULL, enabled_from DATETIME DEFAULT NULL, enabled_to DATETIME DEFAULT NULL, is_default TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, locale VARCHAR(6) DEFAULT NULL, title VARCHAR(64) DEFAULT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, revtype VARCHAR(4) NOT NULL, PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page__snapshot (id INT AUTO_INCREMENT NOT NULL, page_id INT DEFAULT NULL, site_id INT DEFAULT NULL, route_name VARCHAR(255) NOT NULL, page_alias VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, decorate TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, url LONGTEXT DEFAULT NULL, parent_id INT DEFAULT NULL, target_id INT DEFAULT NULL, content LONGTEXT DEFAULT NULL COMMENT '(DC2Type:json)', publication_date_start DATETIME DEFAULT NULL, publication_date_end DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3963EF9AF6BD1646 (site_id), INDEX IDX_3963EF9AC4663E4 (page_id), INDEX idx_snapshot_dates_enabled (publication_date_start, publication_date_end, enabled), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE page__snapshot_audit (id INT NOT NULL, rev INT NOT NULL, site_id INT DEFAULT NULL, page_id INT DEFAULT NULL, route_name VARCHAR(255) DEFAULT NULL, page_alias VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, decorate TINYINT(1) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, url LONGTEXT DEFAULT NULL, parent_id INT DEFAULT NULL, target_id INT DEFAULT NULL, content LONGTEXT DEFAULT NULL COMMENT '(DC2Type:json)', publication_date_start DATETIME DEFAULT NULL, publication_date_end DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, revtype VARCHAR(4) NOT NULL, PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE phpcr_binarydata (id INT AUTO_INCREMENT NOT NULL, node_id INT NOT NULL, property_name VARCHAR(255) NOT NULL, workspace_name VARCHAR(255) NOT NULL, idx INT DEFAULT 0 NOT NULL, data LONGBLOB NOT NULL, UNIQUE INDEX UNIQ_37E65615460D9FD7413BC13C1AC10DC4E7087E10 (node_id, property_name, workspace_name, idx), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE phpcr_internal_index_types (type VARCHAR(255) NOT NULL, node_id INT NOT NULL, PRIMARY KEY(type, node_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE phpcr_namespaces (prefix VARCHAR(255) NOT NULL, uri VARCHAR(255) NOT NULL, PRIMARY KEY(prefix)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE phpcr_nodes (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, parent VARCHAR(255) NOT NULL, local_name VARCHAR(255) NOT NULL, namespace VARCHAR(255) NOT NULL, workspace_name VARCHAR(255) NOT NULL, identifier VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, props LONGTEXT NOT NULL, depth INT NOT NULL, sort_order INT DEFAULT NULL, UNIQUE INDEX UNIQ_A4624AD7B548B0F1AC10DC4 (path, workspace_name), UNIQUE INDEX UNIQ_A4624AD7772E836A (identifier), INDEX IDX_A4624AD73D8E604F (parent), INDEX IDX_A4624AD78CDE5729 (type), INDEX IDX_A4624AD7623C14D533E16B56 (local_name, namespace), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE phpcr_nodes_references (source_id INT NOT NULL, source_property_name VARCHAR(220) NOT NULL, target_id INT NOT NULL, INDEX IDX_F3BF7E1158E0B66 (target_id), INDEX IDX_F3BF7E1953C1C61 (source_id), PRIMARY KEY(source_id, source_property_name, target_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE phpcr_nodes_weakreferences (source_id INT NOT NULL, source_property_name VARCHAR(220) NOT NULL, target_id INT NOT NULL, INDEX IDX_F0E4F6FA158E0B66 (target_id), INDEX IDX_F0E4F6FA953C1C61 (source_id), PRIMARY KEY(source_id, source_property_name, target_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE phpcr_type_childs (node_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, protected TINYINT(1) NOT NULL, auto_created TINYINT(1) NOT NULL, mandatory TINYINT(1) NOT NULL, on_parent_version INT NOT NULL, primary_types VARCHAR(255) NOT NULL, default_type VARCHAR(255) DEFAULT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE phpcr_type_nodes (node_type_id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, supertypes VARCHAR(255) NOT NULL, is_abstract TINYINT(1) NOT NULL, is_mixin TINYINT(1) NOT NULL, queryable TINYINT(1) NOT NULL, orderable_child_nodes TINYINT(1) NOT NULL, primary_item VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_34B0A8095E237E06 (name), PRIMARY KEY(node_type_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE phpcr_type_props (node_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, protected TINYINT(1) NOT NULL, auto_created TINYINT(1) NOT NULL, mandatory TINYINT(1) NOT NULL, on_parent_version INT NOT NULL, multiple TINYINT(1) NOT NULL, fulltext_searchable TINYINT(1) NOT NULL, query_orderable TINYINT(1) NOT NULL, required_type INT NOT NULL, query_operators INT NOT NULL, default_value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(node_type_id, name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE phpcr_workspaces (name VARCHAR(255) NOT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE page__block ADD CONSTRAINT FK_66F58DA0727ACA70 FOREIGN KEY (parent_id) REFERENCES page__block (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page__block ADD CONSTRAINT FK_66F58DA0C4663E4 FOREIGN KEY (page_id) REFERENCES page__page (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page__page ADD CONSTRAINT FK_2FAE39ED158E0B66 FOREIGN KEY (target_id) REFERENCES page__page (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page__page ADD CONSTRAINT FK_2FAE39ED727ACA70 FOREIGN KEY (parent_id) REFERENCES page__page (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page__page ADD CONSTRAINT FK_2FAE39EDF6BD1646 FOREIGN KEY (site_id) REFERENCES page__site (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page__snapshot ADD CONSTRAINT FK_3963EF9AC4663E4 FOREIGN KEY (page_id) REFERENCES page__page (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE page__snapshot ADD CONSTRAINT FK_3963EF9AF6BD1646 FOREIGN KEY (site_id) REFERENCES page__site (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE phpcr_nodes_references ADD CONSTRAINT FK_F3BF7E1953C1C61 FOREIGN KEY (source_id) REFERENCES phpcr_nodes (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE phpcr_nodes_weakreferences ADD CONSTRAINT FK_F0E4F6FA158E0B66 FOREIGN KEY (target_id) REFERENCES phpcr_nodes (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE phpcr_nodes_weakreferences ADD CONSTRAINT FK_F0E4F6FA953C1C61 FOREIGN KEY (source_id) REFERENCES phpcr_nodes (id) ON DELETE CASCADE");
    }
}
