import { useEffect, useState } from "react";

interface Question {
    id: number;
    type: 'radio' | 'checkbox';
    name: string;
    options: QuestionOption[];
}

interface QuestionOption {
    id: number;
    question_id: number;
    value: string;
    order: number | null;
}

interface Answer {
    question_id: number;
    value: string[];
}

const fetchQuestions = async (): Promise<Question[]> => {
    const response = await fetch('/api/questions');

    if (!response.ok) {
        throw new Error('Failed to fetch questions');
    }

    return await response.json();
}

export const Form = () => {
    const [isSubmitted, setIsSubmitted] = useState(false);
    const [errors, setErrors] = useState('');
    const [questions, setQuestions] = useState<Question[]>([]);
    const [answers, setAnswers] = useState<Answer[]>([])

    useEffect(() => {
        fetchQuestions().then((questions) => {
            setQuestions(questions);
        });
    }, []);

    const handleRadioChange = (e: React.ChangeEvent<HTMLInputElement>, questionId: number) => {
        let isAlreadyAnswered = answers.find((answer) => answer.question_id === questionId) !== undefined

        if (isAlreadyAnswered) {
            setAnswers(answers.map((answer) => {
                return answer.question_id === questionId
                    ? { ...answer, value: [e.target.value] }
                    : answer;
            }))
        } else {
            setAnswers([...answers, { question_id: questionId, value: [e.target.value] }])
        }
    }

    const handleCheckboxChange = (e: React.ChangeEvent<HTMLInputElement>, questionId: number) => {
        const currentAnswer = answers.find((answer) => answer.question_id === questionId)

        if (e.target.checked) {
            if (!currentAnswer) {
                // add new answer
                setAnswers([...answers, { question_id: questionId, value: [e.target.value] }])
            } else {
                // update existing answer
                setAnswers(answers.map((answer): Answer => {
                    return answer.question_id === questionId
                        ? { ...answer, value: [...answer.value, e.target.value] }
                        : answer
                }))
            }
        } else {
            if (currentAnswer) {
                // remove answer if unchecked the only selected option
                let updatedAnswers = answers.filter(answer => {
                    const uncheckedAllOptions = answer.question_id === questionId
                        && answer.value.length === 1
                        && answer.value[0] === e.target.value

                    return answer.question_id !== questionId || !uncheckedAllOptions;
                })

                // remove unchecked option from answer
                updatedAnswers = updatedAnswers.map(answer =>
                    answer.question_id !== questionId
                        ? answer
                        : {
                            ...answer,
                            value: answer.value.filter(selectedOption => selectedOption !== e.target.value)
                        }
                )

                setAnswers(updatedAnswers);
            }
        }
    }

    const handleSubmit = async (e: React.SubmitEvent<HTMLFormElement>) => {
        e.preventDefault();
        
        const url = '/api/responses'

        const response = await fetch(url, {
            method: 'POST',
            body: JSON.stringify(answers),
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })

        if (!response.ok) {
            if (response.status >= 400) {
                const data = await response.json();
                setErrors(data.message);
            }
            return;
        }

        setIsSubmitted(true);
    }

    if (errors) {
        return (
            <>
                <p>There was a problem submitting the form: <span style={{ color: 'red' }}>{errors}</span></p>
                <p>Please reload the page.</p>
            </>
        )
    }

    if (isSubmitted) {
        return <p>Form submitted successfully.</p>;
    }

    return (
        <form onSubmit={handleSubmit}>
            {questions.map((question) => {
                return (
                    <fieldset key={question.id}>
                        <legend>{question.name}</legend>

                        {/* TODO: add sorting based on question order */}
                        {question.options.map((option) => {
                            if (question.type === 'radio') {
                                return (
                                    <p key={option.id}>
                                        <label>
                                            {option.value}

                                            <input
                                                type={question.type}
                                                name={String(question.id)}
                                                value={option.value}
                                                onChange={(e) => handleRadioChange(e, question.id)}
                                            />
                                        </label>
                                    </p>
                                )
                            }

                            if (question.type === 'checkbox') {
                                return (
                                    <p key={option.id}>
                                        <label>
                                            {option.value}

                                            <input
                                                type={question.type}
                                                name={String(question.id)}
                                                value={option.value}
                                                onChange={(e) => handleCheckboxChange(e, question.id)}
                                            />
                                        </label>
                                    </p>
                                )
                            }

                            return null
                        })}
                    </fieldset>
                )
            })}

            <button>Submit</button>
        </form>
    );
}