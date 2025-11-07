<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite\Admin;

/**
 * Ensures the Pods manage table lists the quota field.
 */
final class CodesQuotaColumn {
	private const FIELD_NAME = 'rna_wss_codes_quota';

	public function register(): void {
		add_filter('pods_admin_ui_fields_rna_wss_code', [$this, 'addQuotaColumn'], 10, 3);
	}

	/**
	 * @param array<string, array<string, mixed>> $fields
	 * @param array<string, mixed>|null           $pod
	 * @param mixed                               $pods
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function addQuotaColumn(array $fields, $pod = null, $pods = null): array {
		if (isset($fields[self::FIELD_NAME])) {
			return $fields;
		}

		$fields[self::FIELD_NAME] = $this->resolveQuotaField($pod);

		return $fields;
	}

	/**
	 * @param array<string, mixed>|null $pod
	 *
	 * @return array<string, mixed>
	 */
	private function resolveQuotaField($pod): array {
		if (is_array($pod)) {
			if (isset($pod['fields'][self::FIELD_NAME]) && is_array($pod['fields'][self::FIELD_NAME])) {
				return $pod['fields'][self::FIELD_NAME];
			}

			if (isset($pod['object_fields'][self::FIELD_NAME]) && is_array($pod['object_fields'][self::FIELD_NAME])) {
				return $pod['object_fields'][self::FIELD_NAME];
			}
		}

		$field = [
			'name'  => self::FIELD_NAME,
			'label' => __('Kontingent', 'runa-mod-workshop-suite'),
			'type'  => 'number',
			'width' => '10%',
		];

		if (class_exists('\PodsForm')) {
			/** @var array<string, mixed> $field */
			$field = \PodsForm::field_setup($field, null, $field['type']);
		}

		return $field;
	}
}
