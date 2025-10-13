<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'team_create',
            ],
            [
                'id'    => 18,
                'title' => 'team_edit',
            ],
            [
                'id'    => 19,
                'title' => 'team_show',
            ],
            [
                'id'    => 20,
                'title' => 'team_delete',
            ],
            [
                'id'    => 21,
                'title' => 'team_access',
            ],
            [
                'id'    => 22,
                'title' => 'user_alert_create',
            ],
            [
                'id'    => 23,
                'title' => 'user_alert_show',
            ],
            [
                'id'    => 24,
                'title' => 'user_alert_delete',
            ],
            [
                'id'    => 25,
                'title' => 'user_alert_access',
            ],
            [
                'id'    => 26,
                'title' => 'expense_management_access',
            ],
            [
                'id'    => 27,
                'title' => 'expense_category_create',
            ],
            [
                'id'    => 28,
                'title' => 'expense_category_edit',
            ],
            [
                'id'    => 29,
                'title' => 'expense_category_show',
            ],
            [
                'id'    => 30,
                'title' => 'expense_category_delete',
            ],
            [
                'id'    => 31,
                'title' => 'expense_category_access',
            ],
            [
                'id'    => 32,
                'title' => 'income_category_create',
            ],
            [
                'id'    => 33,
                'title' => 'income_category_edit',
            ],
            [
                'id'    => 34,
                'title' => 'income_category_show',
            ],
            [
                'id'    => 35,
                'title' => 'income_category_delete',
            ],
            [
                'id'    => 36,
                'title' => 'income_category_access',
            ],
            [
                'id'    => 37,
                'title' => 'expense_create',
            ],
            [
                'id'    => 38,
                'title' => 'expense_edit',
            ],
            [
                'id'    => 39,
                'title' => 'expense_show',
            ],
            [
                'id'    => 40,
                'title' => 'expense_delete',
            ],
            [
                'id'    => 41,
                'title' => 'expense_access',
            ],
            [
                'id'    => 42,
                'title' => 'income_create',
            ],
            [
                'id'    => 43,
                'title' => 'income_edit',
            ],
            [
                'id'    => 44,
                'title' => 'income_show',
            ],
            [
                'id'    => 45,
                'title' => 'income_delete',
            ],
            [
                'id'    => 46,
                'title' => 'income_access',
            ],
            [
                'id'    => 47,
                'title' => 'expense_report_create',
            ],
            [
                'id'    => 48,
                'title' => 'expense_report_edit',
            ],
            [
                'id'    => 49,
                'title' => 'expense_report_show',
            ],
            [
                'id'    => 50,
                'title' => 'expense_report_delete',
            ],
            [
                'id'    => 51,
                'title' => 'expense_report_access',
            ],
            [
                'id'    => 52,
                'title' => 'client_management_setting_access',
            ],
            [
                'id'    => 53,
                'title' => 'currency_create',
            ],
            [
                'id'    => 54,
                'title' => 'currency_edit',
            ],
            [
                'id'    => 55,
                'title' => 'currency_show',
            ],
            [
                'id'    => 56,
                'title' => 'currency_delete',
            ],
            [
                'id'    => 57,
                'title' => 'currency_access',
            ],
            [
                'id'    => 58,
                'title' => 'transaction_type_create',
            ],
            [
                'id'    => 59,
                'title' => 'transaction_type_edit',
            ],
            [
                'id'    => 60,
                'title' => 'transaction_type_show',
            ],
            [
                'id'    => 61,
                'title' => 'transaction_type_delete',
            ],
            [
                'id'    => 62,
                'title' => 'transaction_type_access',
            ],
            [
                'id'    => 63,
                'title' => 'income_source_create',
            ],
            [
                'id'    => 64,
                'title' => 'income_source_edit',
            ],
            [
                'id'    => 65,
                'title' => 'income_source_show',
            ],
            [
                'id'    => 66,
                'title' => 'income_source_delete',
            ],
            [
                'id'    => 67,
                'title' => 'income_source_access',
            ],
            [
                'id'    => 68,
                'title' => 'client_status_create',
            ],
            [
                'id'    => 69,
                'title' => 'client_status_edit',
            ],
            [
                'id'    => 70,
                'title' => 'client_status_show',
            ],
            [
                'id'    => 71,
                'title' => 'client_status_delete',
            ],
            [
                'id'    => 72,
                'title' => 'client_status_access',
            ],
            [
                'id'    => 73,
                'title' => 'project_status_create',
            ],
            [
                'id'    => 74,
                'title' => 'project_status_edit',
            ],
            [
                'id'    => 75,
                'title' => 'project_status_show',
            ],
            [
                'id'    => 76,
                'title' => 'project_status_delete',
            ],
            [
                'id'    => 77,
                'title' => 'project_status_access',
            ],
            [
                'id'    => 78,
                'title' => 'client_management_access',
            ],
            [
                'id'    => 79,
                'title' => 'client_create',
            ],
            [
                'id'    => 80,
                'title' => 'client_edit',
            ],
            [
                'id'    => 81,
                'title' => 'client_show',
            ],
            [
                'id'    => 82,
                'title' => 'client_delete',
            ],
            [
                'id'    => 83,
                'title' => 'client_access',
            ],
            [
                'id'    => 84,
                'title' => 'project_create',
            ],
            [
                'id'    => 85,
                'title' => 'project_edit',
            ],
            [
                'id'    => 86,
                'title' => 'project_show',
            ],
            [
                'id'    => 87,
                'title' => 'project_delete',
            ],
            [
                'id'    => 88,
                'title' => 'project_access',
            ],
            [
                'id'    => 89,
                'title' => 'note_create',
            ],
            [
                'id'    => 90,
                'title' => 'note_edit',
            ],
            [
                'id'    => 91,
                'title' => 'note_show',
            ],
            [
                'id'    => 92,
                'title' => 'note_delete',
            ],
            [
                'id'    => 93,
                'title' => 'note_access',
            ],
            [
                'id'    => 94,
                'title' => 'document_create',
            ],
            [
                'id'    => 95,
                'title' => 'document_edit',
            ],
            [
                'id'    => 96,
                'title' => 'document_show',
            ],
            [
                'id'    => 97,
                'title' => 'document_delete',
            ],
            [
                'id'    => 98,
                'title' => 'document_access',
            ],
            [
                'id'    => 99,
                'title' => 'transaction_create',
            ],
            [
                'id'    => 100,
                'title' => 'transaction_edit',
            ],
            [
                'id'    => 101,
                'title' => 'transaction_show',
            ],
            [
                'id'    => 102,
                'title' => 'transaction_delete',
            ],
            [
                'id'    => 103,
                'title' => 'transaction_access',
            ],
            [
                'id'    => 104,
                'title' => 'client_report_create',
            ],
            [
                'id'    => 105,
                'title' => 'client_report_edit',
            ],
            [
                'id'    => 106,
                'title' => 'client_report_show',
            ],
            [
                'id'    => 107,
                'title' => 'client_report_delete',
            ],
            [
                'id'    => 108,
                'title' => 'client_report_access',
            ],
            [
                'id'    => 109,
                'title' => 'asset_management_access',
            ],
            [
                'id'    => 110,
                'title' => 'asset_category_create',
            ],
            [
                'id'    => 111,
                'title' => 'asset_category_edit',
            ],
            [
                'id'    => 112,
                'title' => 'asset_category_show',
            ],
            [
                'id'    => 113,
                'title' => 'asset_category_delete',
            ],
            [
                'id'    => 114,
                'title' => 'asset_category_access',
            ],
            [
                'id'    => 115,
                'title' => 'asset_location_create',
            ],
            [
                'id'    => 116,
                'title' => 'asset_location_edit',
            ],
            [
                'id'    => 117,
                'title' => 'asset_location_show',
            ],
            [
                'id'    => 118,
                'title' => 'asset_location_delete',
            ],
            [
                'id'    => 119,
                'title' => 'asset_location_access',
            ],
            [
                'id'    => 120,
                'title' => 'asset_status_create',
            ],
            [
                'id'    => 121,
                'title' => 'asset_status_edit',
            ],
            [
                'id'    => 122,
                'title' => 'asset_status_show',
            ],
            [
                'id'    => 123,
                'title' => 'asset_status_delete',
            ],
            [
                'id'    => 124,
                'title' => 'asset_status_access',
            ],
            [
                'id'    => 125,
                'title' => 'asset_create',
            ],
            [
                'id'    => 126,
                'title' => 'asset_edit',
            ],
            [
                'id'    => 127,
                'title' => 'asset_show',
            ],
            [
                'id'    => 128,
                'title' => 'asset_delete',
            ],
            [
                'id'    => 129,
                'title' => 'asset_access',
            ],
            [
                'id'    => 130,
                'title' => 'assets_history_access',
            ],
            [
                'id'    => 131,
                'title' => 'product_access',
            ],
            [
                'id'    => 132,
                'title' => 'vt_create',
            ],
            [
                'id'    => 133,
                'title' => 'vt_edit',
            ],
            [
                'id'    => 134,
                'title' => 'vt_show',
            ],
            [
                'id'    => 135,
                'title' => 'vt_delete',
            ],
            [
                'id'    => 136,
                'title' => 'vt_access',
            ],
            [
                'id'    => 137,
                'title' => 'imei_model_create',
            ],
            [
                'id'    => 138,
                'title' => 'imei_model_edit',
            ],
            [
                'id'    => 139,
                'title' => 'imei_model_show',
            ],
            [
                'id'    => 140,
                'title' => 'imei_model_delete',
            ],
            [
                'id'    => 141,
                'title' => 'imei_model_access',
            ],
            [
                'id'    => 142,
                'title' => 'imei_master_create',
            ],
            [
                'id'    => 143,
                'title' => 'imei_master_edit',
            ],
            [
                'id'    => 144,
                'title' => 'imei_master_show',
            ],
            [
                'id'    => 145,
                'title' => 'imei_master_delete',
            ],
            [
                'id'    => 146,
                'title' => 'imei_master_access',
            ],
            [
                'id'    => 147,
                'title' => 'product_model_create',
            ],
            [
                'id'    => 148,
                'title' => 'product_model_edit',
            ],
            [
                'id'    => 149,
                'title' => 'product_model_show',
            ],
            [
                'id'    => 150,
                'title' => 'product_model_delete',
            ],
            [
                'id'    => 151,
                'title' => 'product_model_access',
            ],
            [
                'id'    => 152,
                'title' => 'product_master_create',
            ],
            [
                'id'    => 153,
                'title' => 'product_master_edit',
            ],
            [
                'id'    => 154,
                'title' => 'product_master_show',
            ],
            [
                'id'    => 155,
                'title' => 'product_master_delete',
            ],
            [
                'id'    => 156,
                'title' => 'product_master_access',
            ],
            [
                'id'    => 157,
                'title' => 'unbind_product_create',
            ],
            [
                'id'    => 158,
                'title' => 'unbind_product_edit',
            ],
            [
                'id'    => 159,
                'title' => 'unbind_product_show',
            ],
            [
                'id'    => 160,
                'title' => 'unbind_product_delete',
            ],
            [
                'id'    => 161,
                'title' => 'unbind_product_access',
            ],
            [
                'id'    => 162,
                'title' => 'stock_access',
            ],
            [
                'id'    => 163,
                'title' => 'current_stock_create',
            ],
            [
                'id'    => 164,
                'title' => 'current_stock_edit',
            ],
            [
                'id'    => 165,
                'title' => 'current_stock_show',
            ],
            [
                'id'    => 166,
                'title' => 'current_stock_delete',
            ],
            [
                'id'    => 167,
                'title' => 'current_stock_access',
            ],
            [
                'id'    => 168,
                'title' => 'stock_transfer_create',
            ],
            [
                'id'    => 169,
                'title' => 'stock_transfer_edit',
            ],
            [
                'id'    => 170,
                'title' => 'stock_transfer_show',
            ],
            [
                'id'    => 171,
                'title' => 'stock_transfer_delete',
            ],
            [
                'id'    => 172,
                'title' => 'stock_transfer_access',
            ],
            [
                'id'    => 173,
                'title' => 'check_party_stock_create',
            ],
            [
                'id'    => 174,
                'title' => 'check_party_stock_edit',
            ],
            [
                'id'    => 175,
                'title' => 'check_party_stock_show',
            ],
            [
                'id'    => 176,
                'title' => 'check_party_stock_delete',
            ],
            [
                'id'    => 177,
                'title' => 'check_party_stock_access',
            ],
            [
                'id'    => 178,
                'title' => 'complain_management_access',
            ],
            [
                'id'    => 179,
                'title' => 'check_complain_create',
            ],
            [
                'id'    => 180,
                'title' => 'check_complain_edit',
            ],
            [
                'id'    => 181,
                'title' => 'check_complain_show',
            ],
            [
                'id'    => 182,
                'title' => 'check_complain_delete',
            ],
            [
                'id'    => 183,
                'title' => 'check_complain_access',
            ],
            [
                'id'    => 184,
                'title' => 'master_access',
            ],
            [
                'id'    => 185,
                'title' => 'location_access',
            ],
            [
                'id'    => 186,
                'title' => 'state_create',
            ],
            [
                'id'    => 187,
                'title' => 'state_edit',
            ],
            [
                'id'    => 188,
                'title' => 'state_show',
            ],
            [
                'id'    => 189,
                'title' => 'state_delete',
            ],
            [
                'id'    => 190,
                'title' => 'state_access',
            ],
            [
                'id'    => 191,
                'title' => 'district_create',
            ],
            [
                'id'    => 192,
                'title' => 'district_edit',
            ],
            [
                'id'    => 193,
                'title' => 'district_show',
            ],
            [
                'id'    => 194,
                'title' => 'district_delete',
            ],
            [
                'id'    => 195,
                'title' => 'district_access',
            ],
            [
                'id'    => 196,
                'title' => 'vehicle_access',
            ],
            [
                'id'    => 197,
                'title' => 'vehicle_type_create',
            ],
            [
                'id'    => 198,
                'title' => 'vehicle_type_edit',
            ],
            [
                'id'    => 199,
                'title' => 'vehicle_type_show',
            ],
            [
                'id'    => 200,
                'title' => 'vehicle_type_delete',
            ],
            [
                'id'    => 201,
                'title' => 'vehicle_type_access',
            ],
            [
                'id'    => 202,
                'title' => 'activation_access',
            ],
            [
                'id'    => 203,
                'title' => 'app_link_create',
            ],
            [
                'id'    => 204,
                'title' => 'app_link_edit',
            ],
            [
                'id'    => 205,
                'title' => 'app_link_show',
            ],
            [
                'id'    => 206,
                'title' => 'app_link_delete',
            ],
            [
                'id'    => 207,
                'title' => 'app_link_access',
            ],
            [
                'id'    => 208,
                'title' => 'activation_request_create',
            ],
            [
                'id'    => 209,
                'title' => 'activation_request_edit',
            ],
            [
                'id'    => 210,
                'title' => 'activation_request_show',
            ],
            [
                'id'    => 211,
                'title' => 'activation_request_delete',
            ],
            [
                'id'    => 212,
                'title' => 'activation_request_access',
            ],
            [
                'id'    => 213,
                'title' => 'attach_veichle_create',
            ],
            [
                'id'    => 214,
                'title' => 'attach_veichle_edit',
            ],
            [
                'id'    => 215,
                'title' => 'attach_veichle_show',
            ],
            [
                'id'    => 216,
                'title' => 'attach_veichle_delete',
            ],
            [
                'id'    => 217,
                'title' => 'attach_veichle_access',
            ],
            [
                'id'    => 218,
                'title' => 'recharge_access',
            ],
            [
                'id'    => 219,
                'title' => 'recharge_plan_create',
            ],
            [
                'id'    => 220,
                'title' => 'recharge_plan_edit',
            ],
            [
                'id'    => 221,
                'title' => 'recharge_plan_show',
            ],
            [
                'id'    => 222,
                'title' => 'recharge_plan_delete',
            ],
            [
                'id'    => 223,
                'title' => 'recharge_plan_access',
            ],
            [
                'id'    => 224,
                'title' => 'recharge_request_create',
            ],
            [
                'id'    => 225,
                'title' => 'recharge_request_edit',
            ],
            [
                'id'    => 226,
                'title' => 'recharge_request_show',
            ],
            [
                'id'    => 227,
                'title' => 'recharge_request_delete',
            ],
            [
                'id'    => 228,
                'title' => 'recharge_request_access',
            ],
            [
                'id'    => 229,
                'title' => 'profile_password_edit',
            ],
        ];

        Permission::insert($permissions);
    }
}
