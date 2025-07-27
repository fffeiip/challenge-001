<?php
namespace Core;

interface MigrationInterface
{
    public function up(): void;
    public function down(): void;
}