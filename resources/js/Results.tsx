import { useEffect, useState } from "react";

interface Result {
    question_id: number;
    question_name: string;
    answer_value: string[];
    answer_value_count: number;
    max_answer_value_count: number;
}

export const Results = () => {
    const [results, setResults] = useState<Result[]>([]);

    const fetchResults = async () => {
        const response = await fetch('/api/results');
        const data = await response.json();

        setResults(data);
    }
    
    useEffect(() => {
        fetchResults();
    }, []);

    return (
        <>
            <h2>Results:</h2>

            <h3>Most picked answers by happy people:</h3>
            <table>
                <tbody>
                    {results.map((result) => (
                        <tr key={result.question_id}>
                            <th>{result.question_name}</th>
                            <td>{result.answer_value.join(', ')} {result.answer_value.length > 1 && '(tied)'}</td>
                        </tr>
                    ))}
                    <tr>
                        <th>What programming languages do you know?</th>
                        <td>Ruby</td>
                    </tr>
                </tbody>
            </table>
        </>
        
    )
}