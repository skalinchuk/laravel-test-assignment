<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Credit\Services\Rules;

use App\Domain\Client\Entities\Client;
use App\Domain\Client\ValueObjects\ClientPin;
use App\Domain\Client\ValueObjects\CreditScore;
use App\Domain\Client\ValueObjects\Email;
use App\Domain\Client\ValueObjects\Income;
use App\Domain\Client\ValueObjects\Phone;
use App\Domain\Client\ValueObjects\Region;
use App\Domain\Credit\Entities\Credit;
use App\Domain\Credit\Services\Rules\AgeRule;
use App\Domain\Credit\Services\Rules\CreditScoreRule;
use App\Domain\Credit\Services\Rules\IncomeRule;
use App\Domain\Credit\ValueObjects\CreditAmount;
use App\Domain\Credit\ValueObjects\InterestRate;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CreditRulesTest extends TestCase
{
    private function createClient(
        int $age = 30,
        string $region = 'PR',
        int $income = 1500,
        int $score = 600
    ): Client {
        return new Client(
            id: 'test-id',
            name: 'Test User',
            age: $age,
            region: new Region($region),
            income: new Income($income),
            score: new CreditScore($score),
            pin: new ClientPin('123-45-6789'),
            email: new Email('test@example.com'),
            phone: new Phone('+420123456789')
        );
    }

    private function createCredit(): Credit
    {
        return new Credit(
            id: 'test-credit',
            name: 'Test Credit',
            amount: new CreditAmount(1000.0),
            rate: new InterestRate(10.0),
            startDate: new DateTimeImmutable('2024-01-01'),
            endDate: new DateTimeImmutable('2024-12-31')
        );
    }

    public function test_age_rule_passes_for_valid_age(): void
    {
        $rule = new AgeRule(18, 60);
        $client = $this->createClient(age: 30);
        $credit = $this->createCredit();

        $result = $rule->evaluate($client, $credit);

        $this->assertTrue($result->passed());
        $this->assertEquals('Age requirement met', $result->getReason());
    }

    public function test_age_rule_fails_for_too_young(): void
    {
        $rule = new AgeRule(18, 60);
        $client = $this->createClient(age: 17);
        $credit = $this->createCredit();

        $result = $rule->evaluate($client, $credit);

        $this->assertFalse($result->passed());
        $this->assertEquals('Age must be between 18 and 60', $result->getReason());
    }

    public function test_age_rule_fails_for_too_old(): void
    {
        $rule = new AgeRule(18, 60);
        $client = $this->createClient(age: 61);
        $credit = $this->createCredit();

        $result = $rule->evaluate($client, $credit);

        $this->assertFalse($result->passed());
        $this->assertEquals('Age must be between 18 and 60', $result->getReason());
    }

    public function test_credit_score_rule_passes_for_valid_score(): void
    {
        $rule = new CreditScoreRule(500);
        $client = $this->createClient(score: 600);
        $credit = $this->createCredit();

        $result = $rule->evaluate($client, $credit);

        $this->assertTrue($result->passed());
        $this->assertEquals('Credit score requirement met', $result->getReason());
    }

    public function test_credit_score_rule_fails_for_low_score(): void
    {
        $rule = new CreditScoreRule(500);
        $client = $this->createClient(score: 500);
        $credit = $this->createCredit();

        $result = $rule->evaluate($client, $credit);

        $this->assertFalse($result->passed());
        $this->assertEquals('Credit score must be greater than 500', $result->getReason());
    }

    public function test_income_rule_passes_for_valid_income(): void
    {
        $rule = new IncomeRule(1000);
        $client = $this->createClient(income: 1500);
        $credit = $this->createCredit();

        $result = $rule->evaluate($client, $credit);

        $this->assertTrue($result->passed());
        $this->assertEquals('Income requirement met', $result->getReason());
    }

    public function test_income_rule_fails_for_low_income(): void
    {
        $rule = new IncomeRule(1000);
        $client = $this->createClient(income: 999);
        $credit = $this->createCredit();

        $result = $rule->evaluate($client, $credit);

        $this->assertFalse($result->passed());
        $this->assertEquals('Monthly income must be at least $1000', $result->getReason());
    }
}
