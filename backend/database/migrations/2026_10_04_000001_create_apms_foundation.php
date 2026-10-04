<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        // --- Foundation ---
        Schema::create('companies', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('code')->unique();
            $table->string('address')->nullable(); $table->string('phone')->nullable();
            $table->string('email')->nullable(); $table->string('status')->default('active');
            $table->timestamps(); $table->softDeletes();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name'); $table->string('email'); $table->string('phone')->nullable();
            $table->string('password'); $table->boolean('is_active')->default(true);
            $table->rememberToken(); $table->timestamps(); $table->softDeletes();
            $table->unique(['company_id', 'email']);
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); $table->string('slug')->unique(); $table->string('name');
            $table->string('description')->nullable(); $table->timestamps();
        });
        Schema::create('permissions', function (Blueprint $table) {
            $table->id(); $table->string('slug')->unique(); $table->string('name');
            $table->string('description')->nullable(); $table->timestamps();
        });
        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });
        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
        });
        // --- Inventory ---
        Schema::create('medicines', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name'); $table->string('generic_name')->nullable();
            $table->string('barcode')->nullable(); $table->string('unit')->default('strip');
            $table->string('rack_location')->nullable();
            $table->decimal('reorder_level', 12, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);
            $table->boolean('is_controlled')->default(false);
            $table->boolean('requires_prescription')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps(); $table->softDeletes();
            $table->index(['company_id', 'name']); $table->index(['company_id', 'barcode']);
        });
        Schema::create('batches', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number'); $table->date('expiry_date');
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->timestamps();
            $table->index(['medicine_id', 'expiry_date']);
            $table->unique(['company_id', 'medicine_id', 'batch_number']);
        });
        // --- Suppliers & Purchases ---
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name'); $table->string('phone')->nullable();
            $table->string('email')->nullable(); $table->text('address')->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps(); $table->softDeletes();
        });
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('po_number')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('draft');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 12, 2); $table->decimal('unit_price', 10, 2);
            $table->string('batch_number')->nullable(); $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
        // --- Sales (POS) ---
        Schema::create('sales', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('customer_name')->nullable(); $table->string('customer_phone')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid', 12, 2)->default(0);
            $table->string('payment_method')->default('cash');
            $table->string('idempotency_key')->unique()->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['company_id', 'created_at']);
        });
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 12, 2); $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->text('reason')->nullable();
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        // --- Shifts & Cash ---
        Schema::create('shifts', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('opened_at'); $table->dateTime('closed_at')->nullable();
            $table->decimal('opening_cash', 12, 2)->default(0);
            $table->decimal('closing_cash', 12, 2)->nullable();
            $table->decimal('expected_cash', 12, 2)->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
        });
        // --- Audit & Settings ---
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable(); $table->json('new_values')->nullable();
            $table->json('context')->nullable(); $table->timestamps();
            $table->index(['company_id', 'created_at']);
        });
        Schema::create('settings', function (Blueprint $table) {
            $table->id(); $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('group')->default('general'); $table->string('key');
            $table->json('value')->nullable(); $table->string('type')->default('string');
            $table->timestamps(); $table->unique(['company_id', 'key']);
        });
    }
    public function down(): void {
        foreach (['settings','audit_logs','shifts','sale_returns','sale_items','sales','purchase_items','purchase_orders','suppliers','batches','medicines','role_user','permission_role','permissions','roles','users','companies'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
