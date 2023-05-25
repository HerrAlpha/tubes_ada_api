<?php

namespace Database\Factories;

use App\Models\Enterprise;
use App\Models\RefSetting;
use App\Services\FileManagementService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    private $foodNames = [
        'Gado-Gado', 'Nasi Goreng', 'Mie Goreng', 'Mie Rebus', 'Kwetiau Goreng', 'Kwetiau Rebus',
        'Nasi Uduk', 'Ayam Goreng', 'Ayam Bakar', 'Sate Ayam', 'Sate Kambing', 'Rujak',
        'Soto Ayam', 'Soto Betawi', 'Sop Ayam', 'Sop Ikan', 'Rendang', 'Siomay', 'Batagor',
        'Sayur Lodeh', 'Sayur Asam', 'Sayur Bening', 'Pasta', 'Ketoprak', 'Ikan Bakar',
        'Nasi Uduk', 'Nasi Kuning', 'Sayur Lodeh', 'Gulai', 'Sop Buntut', 'Seblak', 'Sate Lilit',
        'Serabi', 'Kroket', 'Semur', 'Laksa', 'Ayam Geprek', 'Ayam Tangkap', 'Pisang Molen',
        'Otak-Otak', 'Onde-Onde', 'Cireng', 'Gehu', 'Mie Kocok', 'Nasi Timbel', 'Klontong',
        'Tahu Bulat', 'Pepes Ikan', 'Tahu Sumedang', 'Kupat Tahu', 'Tahu Bulat', 'Soto Kuning',
        'Getuk', 'Klepon', 'Nasi Kucing', 'Nasi Pecel', 'Opor Ayam', 'Wajik', 'Urap', 'Babi Guling'

    ];

    private $sauceNames = [
        'Saus Tomat',
        'Pasta Tomat',
        'Saus Mayones',
        'Saus BBQ',
        'Saus Sambal',
        'Saus Bawang Putih',
        'Sambal Terasi',
        'Sambal Cabe Ijo',
        'Sambal Rujak',
        'Sambal Dabu-Dabu',
        'Sambal Matah',
    ];

    private $beverageNames = [
        'Es Teh', 'Es Jeruk', 'Jus Jambu', 'Jus Alpukat', 'Es Buah', 'Es Dawet',
        'Es Kelapa Muda', 'Wedang Ronde', 'Susu Hangat', 'Kopi', 'Teh Panas',
        'Jeruk Hangat', 'Coca-Cola', 'Diet-Coke', 'Es Susu', 'Susu Coklat',
        'Air Putih', 'Sprite', 'Jus Jeruk', 'Es Kopi', 'Es Cendol', 'Skoteng'
    ];

    private $fruitNames = [
        'Lemon',
        'Apel',
        'Pisang',
        'Stroberi',
        'Jeruk',
        'Nanas',
        'Blueberry',
        'Kismis',
        'Kelapa',
        'Anggur',
        'Persik',
        'Frambos',
        'Cranberry',
        'Mangga',
        'Pir',
        'Blackberry',
        'Ceri',
        'Semangka',
        'Kiwi',
        'Pepaya',
        'Jambu Biji',
        'Leci',
        'Nagka',
        'Durian',
        'Sawo',
        'Sirsak',
        'Melon',
        'Jambu',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enterprice         = Enterprise::get()->random(1)->first();
        $margin_price       = RefSetting::where('key', 'margin_price_product')->first();
        $product_pict       = FileManagementService::uploadImage(public_path('images/foods/' . fake()->numberBetween(1, 20) . '.png'), 'food');
        $production_price   = fake()->numberBetween(1, 20) * fake()->randomElement([10000, 100000, 1000000]);
        $price              = $production_price * (100 + ((int) $margin_price->value)) / 100;
        $foodName           = collect($this->foodNames)->random(1)->first();
        $sauceName          = collect($this->sauceNames)->random(1)->first();
        $beverageName       = collect($this->beverageNames)->random(1)->first();
        $fruitName          = collect($this->fruitNames)->random(1)->first();
        $timestamp          = Carbon::create(fake()->datetime());

        $description        = collect(["Paket terdiri dari:", "1 x $foodName $sauceName", "1 x $beverageName", "1 x $fruitName"])->implode("\n");

        return [
            'enterprise_id'     => $enterprice->id,
            'name'              => "Paket $foodName $sauceName",
            'description'       => $description,
            'product_pict'      => $product_pict,
            'price'             => $price,
            'production_price'  => $production_price,
            'created_at'        => $timestamp,
            'updated_at'        => $timestamp
        ];
    }
}
