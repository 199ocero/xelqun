<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Note;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Xelqun\Panels\Notifications\DatabaseNotification;

/**
 * The public demo's data. The same seed gives the same rows every time, so a tab
 * left open still finds its record after a reset, and the demo account stays id 1.
 */
class DemoSeeder extends Seeder
{
    private const int FAKER_SEED = 20261107;

    /** Staff seeded after the demo account. Anything above this id registered itself. */
    public const int SEEDED_USERS = 13;

    private const array BRANDS = ['Northwind' => '#2a78d6', 'Lumen' => '#eda100', 'Kestrel' => '#1baf7a', 'Ferrous' => '#e34948', 'Oakline' => '#8a5a2b', 'Halcyon' => '#6250d6', 'Marlowe' => '#e87ba4', 'Pivot' => '#008300', 'Tessel' => '#eb6834', 'Quill' => '#475569'];

    private const array CATEGORIES = [
        'Keyboards' => ['Mechanical Keyboard', 'Low-Profile Keyboard', 'Wireless Keypad', 'Tenkeyless Board'],
        'Mice' => ['Ergonomic Mouse', 'Travel Mouse', 'Vertical Mouse', 'Silent Trackball'],
        'Monitors' => ['27-inch 4K Monitor', 'Ultrawide Display', 'Portable Monitor', 'Monitor Arm'],
        'Audio' => ['Noise-Cancelling Headphones', 'USB Microphone', 'Desk Speakers', 'Wireless Earbuds'],
        'Storage' => ['Portable SSD', 'NVMe Enclosure', 'Backup Drive', 'SD Card Reader'],
        'Cables' => ['USB-C Cable', 'Thunderbolt Dock', 'HDMI Adapter', 'Braided Charging Cable'],
        'Lighting' => ['Monitor Light Bar', 'Desk Lamp', 'Key Light', 'Ambient Strip'],
        'Furniture' => ['Standing Desk', 'Task Chair', 'Laptop Stand', 'Cable Tray'],
    ];

    private const array POST_TITLES = ['Writing forms in PHP', 'Why we keep tables on the server', 'A tour of the panel shell', 'Tokens, and why the browser gets one', 'Designing a good empty state', 'Live fields without the chatter', 'What a policy should say', 'Soft deletes in practice', 'Tenancy in one afternoon', 'Motion that respects people', 'Theming with one color', 'Two-factor without a package'];

    private const array TOPICS = ['forms', 'tables', 'panels', 'security', 'design'];

    public function run(): void
    {
        $this->reseed();
        $this->seedUsers();
        $this->call([CountrySeeder::class, TagSeeder::class]);

        $countries = Country::query()->orderBy('id')->get();

        $this->reseed();
        $this->seedCatalog();
        $this->reseed();
        $this->seedCustomers($countries);
        $this->reseed();
        $this->seedOrders($countries);
        $this->reseed();
        $this->seedBlog();
        $this->reseed();
        $this->seedWorkspace();

        StoreSetting::query()->create(['store_name' => 'Xelqun Supply', 'support_email' => 'help@example.com', 'currency' => 'USD', 'accepts_orders' => true]);
    }

    /**
     * Each area starts from the same point, so a change to one can't shift the rows
     * of the next. unique() is cleared too, because it remembers what it gave out.
     */
    private function reseed(): void
    {
        fake()->unique(true);
        fake()->seed(self::FAKER_SEED);
    }

    /**
     * Copied after the transaction, because a failed seed shouldn't touch files.
     */
    public function publishImages(): void
    {
        foreach (array_keys(self::CATEGORIES) as $category) {
            $file = $this->imageFor($category);

            Storage::disk('public')->put("demo/products/{$file}", File::get(database_path("seeders/images/{$file}")));
        }
    }

    private function imageFor(string $category): string
    {
        return Str::lower($category).'.svg';
    }

    private function seedUsers(): void
    {
        $demo = User::factory()->create(['name' => 'Ada Demo', 'email' => User::DEMO_EMAIL, 'password' => User::DEMO_PASSWORD, 'email_verified_at' => now()]);

        User::factory()->count(self::SEEDED_USERS - 1)->sequence(fn ($sequence): array => [
            'email' => 'staff'.($sequence->index + 1).'@example.com',
            'created_at' => now()->subDays($sequence->index * 9),
        ])->create();

        foreach (['Your order shipped' => 'success', 'A product is running low' => 'warning', 'New comment waiting for review' => 'info'] as $title => $status) {
            DatabaseNotification::make()->title($title)->{$status}()->sendToDatabase($demo);
        }
    }

    private function seedCatalog(): void
    {
        $brands = [];

        foreach (self::BRANDS as $name => $color) {
            $brands[] = Brand::query()->create([
                'name' => $name,
                'slug' => Str::slug($name),
                'website' => 'https://example.com/'.Str::slug($name),
                'color' => $color,
                'description' => fake()->sentence(12),
                'is_visible' => fake()->boolean(85),
            ])->id;
        }

        $position = 0;

        foreach (self::CATEGORIES as $name => $products) {
            $category = Category::query()->create(['name' => $name, 'sort' => ++$position]);

            foreach (range(1, 15) as $copy) {
                $product = $products[($copy - 1) % count($products)];

                Product::query()->create([
                    'category_id' => $category->id,
                    'brand_id' => fake()->randomElement($brands),
                    'name' => fake()->randomElement(['', 'Pro ', 'Air ', 'Mini ', 'Studio ']).$product,
                    'sku' => fake()->unique()->bothify('??-#####'),
                    'status' => fake()->randomElement(['active', 'active', 'active', 'draft', 'archived']),
                    'price' => fake()->randomFloat(2, 9, 900),
                    'stock' => fake()->numberBetween(0, 250),
                    'is_featured' => fake()->boolean(20),
                    'released_at' => now()->subDays(fake()->numberBetween(1, 700)),
                    'image' => 'demo/products/'.$this->imageFor($name),
                    'description' => '<p>'.fake()->paragraph().'</p>',
                    'specs' => ['Weight' => fake()->numberBetween(90, 2400).' g', 'Warranty' => fake()->randomElement(['1 year', '2 years']), 'Made in' => fake()->randomElement(['Japan', 'Germany', 'Taiwan'])],
                    'keywords' => fake()->randomElements(['wireless', 'usb-c', 'compact', 'studio', 'travel', 'quiet'], 3),
                    'color' => fake()->hexColor(),
                    'discount' => fake()->randomElement([0, 0, 5, 10, 25]),
                    'created_at' => now()->subDays(fake()->numberBetween(1, 300)),
                ]);
            }
        }
    }

    /**
     * @param  Collection<int, Country>  $countries
     */
    private function seedCustomers(Collection $countries): void
    {
        foreach (range(1, 150) as $number) {
            Customer::query()->create([
                'name' => fake()->name(),
                'email' => "customer{$number}@example.com",
                'phone' => fake()->numerify('+1 ### ### ####'),
                'birthday' => fake()->date(max: '-18 years'),
                'country_id' => fake()->randomElement($countries->all())->id,
                'tier' => fake()->randomElement(['standard', 'standard', 'silver', 'gold']),
            ]);
        }
    }

    /**
     * @param  Collection<int, Country>  $countries
     */
    private function seedOrders(Collection $countries): void
    {
        foreach (range(1, 300) as $number) {
            $isBusiness = $number % 3 === 0;
            $order = Order::factory()->create([
                'type' => $isBusiness ? 'business' : 'individual',
                'company' => $isBusiness ? fake()->company() : null,
                'is_gift' => $number % 5 === 0,
                'country_id' => fake()->randomElement($countries->all())->id,
                'created_at' => now()->subDays(fake()->numberBetween(0, 365)),
            ]);

            $items = [];

            foreach (range(1, fake()->numberBetween(1, 4)) as $position) {
                $quantity = fake()->numberBetween(1, 5);
                $price = fake()->randomFloat(2, 10, 300);
                $items[] = ['sort' => $position, 'product' => fake()->randomElement(array_merge(...array_values(self::CATEGORIES))), 'qty' => $quantity, 'price' => $price, 'subtotal' => round($quantity * $price, 2)];
            }

            $order->items()->createMany($items);
            $order->update(['total' => array_sum(array_column($items, 'subtotal'))]);
        }
    }

    private function seedBlog(): void
    {
        $authors = [];

        foreach (['Mara Lindqvist', 'Tomás Herrera', 'Priya Nair', 'Sam Okafor', 'Yuki Tanaka'] as $name) {
            $authors[] = Author::query()->create([
                'name' => $name,
                'email' => Str::slug($name).'@example.com',
                'bio' => fake()->sentence(14),
                'github' => Str::slug($name),
            ])->id;
        }

        $posts = [];

        foreach (range(1, 40) as $number) {
            $title = self::POST_TITLES[($number - 1) % count(self::POST_TITLES)].($number > count(self::POST_TITLES) ? ', part '.ceil($number / count(self::POST_TITLES)) : '');
            $status = fake()->randomElement(['published', 'published', 'review', 'draft']);

            $posts[] = Post::query()->create([
                'author_id' => $number % 9 === 0 ? null : fake()->randomElement($authors),
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => '**'.fake()->sentence(5).'** '.fake()->sentence(12),
                'body' => '<h2>'.fake()->sentence(4).'</h2><p>'.fake()->paragraph(4).'</p><p>'.fake()->paragraph(3).'</p>',
                'blocks' => [
                    ['type' => 'heading', 'data' => ['text' => fake()->sentence(4)]],
                    ['type' => 'paragraph', 'data' => ['text' => fake()->paragraph()]],
                ],
                'snippet' => "TextInput::make('title')\n    ->required()\n    ->maxLength(255);",
                'status' => $status,
                'audience' => fake()->randomElement(['everyone', 'members']),
                'topics' => fake()->randomElements(self::TOPICS, 2),
                'meta' => ['Reading time' => fake()->numberBetween(3, 12).' min', 'Series' => fake()->randomElement(['Basics', 'Deep dives'])],
                'accent' => fake()->hexColor(),
                'rating' => fake()->numberBetween(2, 5),
                'is_pinned' => $number <= 2,
                'published_at' => $status === 'published' ? now()->subDays(fake()->numberBetween(1, 200)) : null,
                'deleted_at' => $number % 13 === 0 ? now()->subDays(2) : null,
            ])->id;
        }

        foreach (range(1, 120) as $number) {
            $status = fake()->randomElement(['approved', 'approved', 'pending', 'spam']);

            Comment::query()->create([
                'commentable_type' => $number % 4 === 0 ? Product::class : Post::class,
                'commentable_id' => $number % 4 === 0 ? fake()->numberBetween(1, 120) : fake()->randomElement($posts),
                'name' => fake()->name(),
                'body' => fake()->sentence(16),
                'status' => $status,
                'is_approved' => $status === 'approved',
            ]);
        }
    }

    private function seedWorkspace(): void
    {
        foreach (['Red team' => 'red', 'Blue team' => 'blue'] as $name => $slug) {
            $team = Team::query()->create(['slug' => $slug, 'name' => $name]);
            $team->users()->attach(1);

            foreach (['Kickoff', 'Roadmap', 'Retro'] as $title) {
                Note::query()->create(['team_id' => $team->id, 'title' => "{$name}: {$title}", 'body' => fake()->sentence(10)]);
            }
        }
    }
}
