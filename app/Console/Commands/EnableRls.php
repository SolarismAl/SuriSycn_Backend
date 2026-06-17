<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EnableRls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:enable-rls';
    protected $description = 'Enable Row Level Security (RLS) on all public tables';

    public function handle()
    {
        \Illuminate\Support\Facades\DB::unprepared("
            DO $$
            DECLARE
                r RECORD;
            BEGIN
                FOR r IN (SELECT tablename FROM pg_tables WHERE schemaname = 'public') LOOP
                    EXECUTE 'ALTER TABLE ' || quote_ident(r.tablename) || ' ENABLE ROW LEVEL SECURITY;';
                END LOOP;
            END $$;
        ");
        
        $this->info('RLS has been enabled on all public tables!');
    }
}
