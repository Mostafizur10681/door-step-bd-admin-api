<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Doorstep Power Solutions™',
                'slug' => 'doorstep-power-solutions',
                'category_tag' => 'HEAVY POWER GENERATION',
                'badge' => 'Flagship Brand',
                'sub_title' => 'GENERATORS & SYNCHRONIZATION',
                'description' => 'Heavy-duty continuous & standby industrial diesel generators, custom soundproof acoustic canopies, and automated load-sharing synchronization systems.',
                'capacity_range' => '50kVA - 3000kVA',
                'warranty_text' => 'Full Factory Warranty & AMC',
                'key_capabilities' => [
                    'Industrial Heavy Diesel Generators',
                    'Custom Acoustic Soundproof Canopies',
                    'Auto-Synchronizing Multi-Genset Panels',
                    'Automatic Main Failure (AMF) Logic',
                ],
                'cta_text' => 'Inquire About Heavy Power Generation',
                'cta_link' => 'tel:+8801700000000',
                'logo' => null,
                'status' => true,
            ],
            [
                'name' => 'Doorstep Solar & Green Energy™',
                'slug' => 'doorstep-solar-green-energy',
                'category_tag' => 'RENEWABLE ENERGY',
                'badge' => 'Eco Certified',
                'sub_title' => 'ON-GRID & HYBRID SOLAR',
                'description' => 'Tier-1 monocrystalline commercial solar arrays, smart string inverters, and battery energy storage systems (BESS) for zero-outage industrial uptime.',
                'capacity_range' => '10kWp - 5MWp',
                'warranty_text' => '25-Year Performance Warranty',
                'key_capabilities' => [
                    'Industrial Rooftop Solar PV Arrays',
                    'Lithium Battery Energy Storage (BESS)',
                    'Net Metering Integration & Approvals',
                    '24/7 Smart Cloud SCADA Monitoring',
                ],
                'cta_text' => 'Inquire About Solar & Green Energy',
                'cta_link' => 'tel:+8801700000000',
                'logo' => null,
                'status' => true,
            ],
            [
                'name' => 'Doorstep Substation & Switchgear™',
                'slug' => 'doorstep-substation-switchgear',
                'category_tag' => 'HIGH VOLTAGE SYSTEMS',
                'badge' => 'Industrial Grade',
                'sub_title' => 'TRANSFORMERS & DISTRIBUTION',
                'description' => 'Complete 11kV/33kV indoor and outdoor package substations, HT/LT vacuum circuit breaker panels, and power factor improvement (PFI) plants.',
                'capacity_range' => '100kVA - 5000kVA',
                'warranty_text' => 'ISO 9001 Certified & AMC',
                'key_capabilities' => [
                    'Cast Resin & Oil Immersed Transformers',
                    'HT Vacuum Circuit Breakers (VCB)',
                    'Automatic Power Factor Improvement',
                    'Type-Tested Low Voltage Panels',
                ],
                'cta_text' => 'Inquire About Substation Solutions',
                'cta_link' => 'tel:+8801700000000',
                'logo' => null,
                'status' => true,
            ],
            [
                'name' => 'Doorstep Industrial Automation™',
                'slug' => 'doorstep-industrial-automation',
                'category_tag' => 'PROCESS AUTOMATION',
                'badge' => 'Smart Tech',
                'sub_title' => 'PLC, SCADA & VFD DRIVES',
                'description' => 'Turnkey industrial control panels, PLC programming, supervisory SCADA dashboards, and variable frequency motor drives for manufacturing automation.',
                'capacity_range' => '0.75kW - 630kW',
                'warranty_text' => 'Comprehensive Tech Support',
                'key_capabilities' => [
                    'Siemens & Schneider PLC Integration',
                    'Custom SCADA & IIoT Gateways',
                    'Energy Optimization VFD Drive Panels',
                    'Smart Factory Instrumentation',
                ],
                'cta_text' => 'Inquire About Automation & PLC',
                'cta_link' => 'tel:+8801700000000',
                'logo' => null,
                'status' => true,
            ],
            [
                'name' => 'Doorstep HVAC & Cleanroom™',
                'slug' => 'doorstep-hvac-cleanroom',
                'category_tag' => 'CLIMATE CONTROL',
                'badge' => 'Precision Tech',
                'sub_title' => 'INDUSTRIAL CHILLERS & AHU',
                'description' => 'Central HVAC water-cooled chillers, air handling units (AHU), ducting systems, and ISO-classified cleanroom environments for pharma & textile.',
                'capacity_range' => '10 TR - 1200 TR',
                'warranty_text' => 'GMP & ASHRAE Compliant',
                'key_capabilities' => [
                    'Magnetic Bearing Centrifugal Chillers',
                    'Hygienic Cleanroom Air Handling Units',
                    'Automated Building Management (BMS)',
                    'Precision Air Conditioning (PAC)',
                ],
                'cta_text' => 'Inquire About HVAC & Cleanrooms',
                'cta_link' => 'tel:+8801700000000',
                'logo' => null,
                'status' => true,
            ],
            [
                'name' => 'Doorstep Water Treatment & ETP™',
                'slug' => 'doorstep-water-treatment-etp',
                'category_tag' => 'ENVIRONMENTAL SYSTEMS',
                'badge' => 'Zero Discharge',
                'sub_title' => 'EFFLUENT & RO TREATMENT',
                'description' => 'Industrial biological & chemical effluent treatment plants (ETP), reverse osmosis (RO) drinking purification, and sewage treatment systems (STP).',
                'capacity_range' => '5m³/hr - 500m³/hr',
                'warranty_text' => 'DoE Environmental Certified',
                'key_capabilities' => [
                    'Biological & MBR Effluent Treatment',
                    'Multi-Stage Industrial RO & EDI',
                    'Zero Liquid Discharge (ZLD) Plants',
                    'Automated pH & Chemical Dosing',
                ],
                'cta_text' => 'Inquire About Water Treatment',
                'cta_link' => 'tel:+8801700000000',
                'logo' => null,
                'status' => true,
            ],
        ];

        foreach ($brands as $item) {
            Brand::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
