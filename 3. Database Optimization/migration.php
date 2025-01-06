<!-- If the table has a large number of rows, the query can be slow because the database needs to scan all rows to find those matching the user_id condition 
before performing the aggregation. I created an index on user_id column and covering index that includes both user_id and amount columns-->

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OptimizeOrdersTableForUserSalesQuery extends Migration
{
    public function up()
    {
        // Add an index to the `user_id` column
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
        });

        // Add a covering index on `user_id` and `amount`
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'amount']);
        });
    }

    public function down()
    {
        // Remove the indexes if the migration is rolled back
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']); // Drop the index on user_id
            $table->dropIndex(['user_id', 'amount']); // Drop the covering index
        });
    }
}
